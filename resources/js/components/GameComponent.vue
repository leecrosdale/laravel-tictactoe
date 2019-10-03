<template>
    <div>

        You are on team: {{ team }}

        <p v-if="current_turn">
            It's your turn
        </p>
        <p v-else>
            It's not your turn
        </p>

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
                    canvas: null,
                    context: null,
                    tileW: 200,
                    tileH: 200,
                    tileCount: 3,
                },
                renderingStatus: 'Not Rendering',
                game: {}
            }
        },
        mounted() {

            this.provider.canvas = this.$refs['game-canvas'];
            this.provider.context = this.$refs['game-canvas'].getContext('2d');

            let self = this;

            this.provider.canvas.addEventListener('mousedown', function (e) {
                self.getCursorPosition(e);
            });
            this.getGame();

            Echo.channel('game').listen('NextTurn', (e) => {
                this.getGame();
            });

            Echo.channel('game').listen('Win', (e) => {

                if (this.current_turn) {
                    alert('You win');
                } else {
                    alert('You lose');
                }
            });

            Echo.channel('game').listen('Draw', (e) => {
                alert('Game Draw');
            });

        },
        props: ['team'],
        computed: {

            current_turn() {
                switch (this.team) {
                    case "x":
                        return this.isEven(this.game.turn_number);
                        break;
                    case "o":
                        return !this.isEven(this.game.turn_number);
                        break;
                }
            }

        },
        methods: {
            getCursorPosition(event) {

                if (this.current_turn) {

                    const rect = this.provider.canvas.getBoundingClientRect();
                    const x = event.clientX - rect.left;
                    const y = event.clientY - rect.top;

                    const box = this.calculateBox(x, y);

                    axios.post('pick', {row: box[0], col: box[1], team: this.team}).then((response) => {
                        this.getGame();
                    });
                } else {
                    alert('not your turn');
                }


            },
            calculateBox(x, y) {

                const row = y < 200 ? 0 : (y > 400 ? 2 : 1);
                const col = x < 200 ? 0 : (x > 400 ? 2 : 1);

                return [row, col];

            },
            isEven(n) {
                return n % 2 === 0;
            },
            getGame() {
                axios.get('update').then((response) => {
                    this.game = response.data;
                    this.render();
                });
            },
            render() {

                if (!this.provider.context) return;
                const ctx = this.provider.context;


                for (let y = 0; y < this.provider.tileCount; y++) {
                    for (let x = 0; x < this.provider.tileCount; x++) {
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


                        for (let k in this.game.picks) {
                            let pick = this.game.picks[k];
                            if (pick.col === x && pick.row === y) {
                                switch(pick.team)
                                {
                                    case "x":
                                        this.drawX(ctx, x * this.provider.tileW + (this.provider.tileW / 2) , y * this.provider.tileH + (this.provider.tileH / 2));
                                    break;

                                    case "o":
                                        this.drawO(ctx, x * this.provider.tileW + (this.provider.tileW / 2) , y * this.provider.tileH + (this.provider.tileH / 2));
                                    break;
                                }

                            }
                        }
                    }
                }
            },
            drawX(ctx, x, y) {
                ctx.beginPath();

                ctx.moveTo(x - 40, y - 40);
                ctx.lineTo(x + 40, y + 40);

                ctx.moveTo(x + 40, y - 40);
                ctx.lineTo(x - 40, y + 40);
                ctx.stroke();
            },
            drawO(ctx, x, y) {
                ctx.beginPath();
                ctx.arc(x, y, 50, 0, 2 * Math.PI);
                ctx.stroke();
            }
        }
    }
</script>
