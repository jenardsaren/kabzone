@props(['route', 'note' => null])

<form method="POST" action="{{ $route }}" class="space-y-4" x-data="approvalSignature()" x-init="init('{{ $note?->approval_signature }}')">
    @csrf
    @method('PATCH')

    <input type="hidden" name="note_section" value="approval">
    <input type="hidden" name="approval_signature" x-model="signature" />

    <div class="grid gap-3 lg:grid-cols-2">
        <div class="space-y-2">
            <p class="text-sm font-semibold text-gray-700">Draw signature</p>
            <canvas x-ref="canvas" class="h-40 w-full rounded border border-gray-300 bg-white" @mousedown="start($event)" @touchstart.prevent="start($event)" @mousemove="draw($event)" @touchmove.prevent="draw($event)" @mouseup="stop" @mouseleave="stop" @touchend="stop"></canvas>
            <div class="flex items-center gap-3">
                <x-secondary-button type="button" @click="reset">Reset</x-secondary-button>
                <span class="text-xs text-gray-500">Signature shows on right after drawing.</span>
            </div>
        </div>

        <div class="space-y-2">
            <p class="text-sm font-semibold text-gray-700">Saved signature</p>
            <div class="h-40 w-full rounded border border-gray-200 bg-white p-2 flex items-center justify-center">
                <template x-if="signature">
                    <img :src="signature" alt="Signature" class="max-h-full" />
                </template>
                <span x-show="! signature" class="text-xs text-gray-400">No signature yet.</span>
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <x-primary-button>Save Approval</x-primary-button>
    </div>

    <script>
        function approvalSignature() {
            return {
                signature: '',
                drawing: false,
                ctx: null,
                init(existing) {
                    this.signature = existing || '';
                    this.ctx = this.$refs.canvas.getContext('2d');
                    this.ctx.lineWidth = 2;
                    this.ctx.strokeStyle = '#111827';
                    if (this.signature) {
                        this.loadImage(this.signature);
                    }
                },
                start(event) {
                    this.drawing = true;
                    this.ctx.beginPath();
                    const { x, y } = this.getCoords(event);
                    this.ctx.moveTo(x, y);
                },
                draw(event) {
                    if (! this.drawing) {
                        return;
                    }
                    const { x, y } = this.getCoords(event);
                    this.ctx.lineTo(x, y);
                    this.ctx.stroke();
                    this.signature = this.$refs.canvas.toDataURL('image/png');
                },
                stop() {
                    if (! this.drawing) {
                        return;
                    }
                    this.drawing = false;
                    this.signature = this.$refs.canvas.toDataURL('image/png');
                },
                reset() {
                    this.ctx.clearRect(0, 0, this.$refs.canvas.width, this.$refs.canvas.height);
                    this.signature = '';
                },
                getCoords(event) {
                    const rect = this.$refs.canvas.getBoundingClientRect();
                    const x = event.touches ? event.touches[0].clientX - rect.left : event.clientX - rect.left;
                    const y = event.touches ? event.touches[0].clientY - rect.top : event.clientY - rect.top;
                    return { x, y };
                },
                loadImage(dataUrl) {
                    const img = new Image();
                    img.onload = () => {
                        this.ctx.drawImage(img, 0, 0, this.$refs.canvas.width, this.$refs.canvas.height);
                    };
                    img.src = dataUrl;
                },
            };
        }
    </script>
</form>
