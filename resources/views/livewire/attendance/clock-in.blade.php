    <div class="flex min-h-full flex-1 flex-col items-center justify-center py-8">
        <div class="w-full max-w-sm">

            <div class="mb-6 text-center">
                <flux:heading size="xl">{{ __('Attendance') }}</flux:heading>
                <flux:text>{{ now()->format('l, F j, Y') }}</flux:text>
            </div>

            {{-- Already done for today --}}
            @if ($this->nextAction === 'done')
                <flux:card class="flex flex-col items-center gap-4 py-10 text-center">
                    <flux:icon.check-circle class="size-16 text-green-500" />
                    <flux:heading>{{ __('Shift complete!') }}</flux:heading>
                    <flux:text>{{ __("You've recorded both time-in and time-out for today.") }}</flux:text>

                    <div class="mt-2 w-full space-y-2">
                        @foreach ($this->todayLogs as $log)
                            <div class="flex items-center justify-between rounded-lg bg-zinc-50 px-4 py-2 dark:bg-zinc-800">
                                <flux:badge :color="$log->type === 'time_in' ? 'green' : 'blue'">
                                    {{ $log->type === 'time_in' ? __('Time In') : __('Time Out') }}
                                </flux:badge>
                                <flux:text>{{ $log->logged_at->format('h:i A') }}</flux:text>
                                @if ($log->isLate())
                                    <flux:badge color="amber">{{ $log->late_minutes }}m {{ __('late') }}</flux:badge>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </flux:card>

            {{-- Camera capture --}}
            @else
                <flux:card
                    x-data="{
                        streaming: false,
                        captured: false,
                        capturedSrc: null,
                        uploading: false,
                        error: null,

                        async startCamera() {
                            this.error = null;
                            try {
                                const stream = await navigator.mediaDevices.getUserMedia({
                                    video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
                                    audio: false
                                });
                                this.$refs.video.srcObject = stream;
                                await this.$refs.video.play();
                                this.streaming = true;

                                if ('geolocation' in navigator) {
                                    navigator.geolocation.getCurrentPosition(pos => {
                                        $wire.set('latitude', pos.coords.latitude);
                                        $wire.set('longitude', pos.coords.longitude);
                                    }, () => {});
                                }
                            } catch (e) {
                                this.error = 'Camera access denied. Please allow camera access and try again.';
                            }
                        },

                        capture() {
                            const canvas = this.$refs.canvas;
                            const video = this.$refs.video;
                            canvas.width = video.videoWidth;
                            canvas.height = video.videoHeight;
                            canvas.getContext('2d').drawImage(video, 0, 0);
                            this.capturedSrc = canvas.toDataURL('image/jpeg', 0.85);
                            this.captured = true;
                            video.srcObject?.getTracks().forEach(t => t.stop());
                        },

                        retake() {
                            this.captured = false;
                            this.capturedSrc = null;
                            this.startCamera();
                        },

                        submit() {
                            this.uploading = true;
                            this.$refs.canvas.toBlob(blob => {
                                const file = new File([blob], 'selfie.jpg', { type: 'image/jpeg' });
                                $wire.upload('selfie', file,
                                    () => { $wire.record(); this.uploading = false; },
                                    () => { this.error = 'Upload failed. Please try again.'; this.uploading = false; },
                                    () => {}
                                );
                            }, 'image/jpeg', 0.85);
                        }
                    }"
                    x-init="startCamera()"
                    class="flex flex-col items-center gap-4 p-4"
                >
                    <flux:heading>
                        {{ $this->nextAction === 'time_in' ? __('Time In') : __('Time Out') }}
                    </flux:heading>

                    {{-- Error --}}
                    <template x-if="error">
                        <flux:callout color="red" icon="exclamation-triangle" class="w-full">
                            <flux:callout.text x-text="error"></flux:callout.text>
                        </flux:callout>
                    </template>

                    {{-- Schedule info --}}
                    @if ($this->todaySchedule)
                        <div class="flex w-full items-center justify-between rounded-lg bg-zinc-50 px-3 py-2 text-sm dark:bg-zinc-800">
                            <flux:text>{{ __('Scheduled shift') }}</flux:text>
                            <flux:text class="font-medium">
                                {{ date('g:i A', strtotime($this->todaySchedule->start_time)) }}
                                – {{ date('g:i A', strtotime($this->todaySchedule->end_time)) }}
                            </flux:text>
                        </div>
                    @endif

                    {{-- Camera / Preview --}}
                    <div class="relative w-full overflow-hidden rounded-xl bg-black" style="aspect-ratio: 4/3">
                        <video
                            x-ref="video"
                            x-show="!captured"
                            class="h-full w-full object-cover"
                            autoplay
                            playsinline
                            muted
                        ></video>
                        <img
                            x-show="captured"
                            :src="capturedSrc"
                            class="h-full w-full object-cover"
                        />
                        <canvas x-ref="canvas" class="hidden"></canvas>

                        {{-- Loading overlay --}}
                        <template x-if="!streaming && !captured && !error">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <flux:icon.camera class="size-12 animate-pulse text-white/50" />
                            </div>
                        </template>
                    </div>

                    {{-- Actions --}}
                    <div class="flex w-full gap-3">
                        <template x-if="streaming && !captured">
                            <flux:button
                                variant="primary"
                                class="w-full"
                                @click="capture()"
                            >
                                <flux:icon.camera class="mr-2 size-4" />
                                {{ __('Take Selfie') }}
                            </flux:button>
                        </template>

                        <template x-if="captured">
                            <flux:button class="flex-1" @click="retake()">
                                {{ __('Retake') }}
                            </flux:button>
                        </template>

                        <template x-if="captured">
                            <flux:button
                                variant="primary"
                                class="flex-1"
                                @click="submit()"
                                :disabled="uploading"
                            >
                                <span x-show="!uploading">
                                    {{ $this->nextAction === 'time_in' ? __('Confirm Time In') : __('Confirm Time Out') }}
                                </span>
                                <span x-show="uploading">{{ __('Saving…') }}</span>
                            </flux:button>
                        </template>
                    </div>

                    {{-- Today's log so far --}}
                    @if ($this->todayLogs->isNotEmpty())
                        <div class="w-full border-t border-zinc-100 pt-3 dark:border-zinc-800">
                            @foreach ($this->todayLogs as $log)
                                <div class="flex items-center justify-between py-1 text-sm">
                                    <flux:badge :color="$log->type === 'time_in' ? 'green' : 'blue'" size="sm">
                                        {{ $log->type === 'time_in' ? __('In') : __('Out') }}
                                    </flux:badge>
                                    <span>{{ $log->logged_at->format('h:i A') }}</span>
                                    @if ($log->isLate())
                                        <flux:badge color="amber" size="sm">{{ $log->late_minutes }}m late</flux:badge>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </flux:card>
            @endif

        </div>
    </div>
