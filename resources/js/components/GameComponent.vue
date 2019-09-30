<template>
    <div>
        <canvas width="600" height="600" style="background: #000;" ref="game-canvas"></canvas>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                // By creating the provider in the data property, it becomes reactive,
                // so child components will update when `context` changes.
                provider: {
                    // This is the CanvasRenderingContext that children will draw to.
                    context: null,
                    tileW: 200,
                    tileH: 200,
                    tileCount: 3,
                },
                renderingStatus: 'Not Rendering'
            }
        },
        mounted() {
            this.provider.context = this.$refs['game-canvas'].getContext('2d');
            this.render()
        },
        methods: {
            render() {

                if (!this.provider.context) return;
                const ctx = this.provider.context;


                for (let y = 0; y<this.provider.tileCount; y++)
                {
                    for (let x = 0; x<this.provider.tileCount; x++)
                    {
                        ctx.beginPath();
                        ctx.rect(
                            x * this.provider.tileW,
                            y * this.provider.tileH,
                            this.provider.tileW,
                            this.provider.tileH
                        );
                        ctx.fillStyle = 'white';
                        ctx.fill();
                        ctx.lineWidth = 2;
                        ctx.strokeStyle = '#003300';
                        ctx.stroke();

                        this.drawX(ctx, x * this.provider.tileW + (this.provider.tileW / 2) , y * this.provider.tileH + (this.provider.tileH / 2));
                        this.drawO(ctx, x * this.provider.tileW + (this.provider.tileW / 2) , y * this.provider.tileH + (this.provider.tileH / 2));
                    }
                }
            },
            drawX(ctx,x, y)
            {
                ctx.beginPath();

                ctx.moveTo(x - 40, y - 40);
                ctx.lineTo(x + 40, y + 40);

                ctx.moveTo(x + 40, y - 40);
                ctx.lineTo(x - 40, y + 40);
                ctx.stroke();
            },
            drawO(ctx, x, y)
            {
                ctx.beginPath();
                ctx.arc(x, y, 50, 0, 2 * Math.PI);
                ctx.stroke();
            }
        }
    }
</script>
