<template>
   <div></div>
</template>

<script>
    export default {
        data: () => ({
            cardStyles: {
                background: 'transparent',
                'box-shadow': 'none',
            }
        }),
        methods: {},
        computed: {}
    }
</script>

<style scoped lang="scss">
    .container {
        text-align: center;
        padding: 0 5%;
    }

    h1 {
        margin-top: 120px;
        text-align: center;
        font-family: EuroStileBlaIta, sans-serif;
        font-size: 75px;
        text-transform: uppercase;
        color: #fff;
        line-height: 87%;
    }

    h3 {
        text-align: center;
        font-family: EuroStileBlaIta, sans-serif;
        font-size: 34px;
        text-transform: uppercase;
        color: #fff;
        line-height: 87%;
        margin-top: 15px;
    }

    .main__link {
        text-transform: uppercase;
        color: #fff!important;
        text-align: center;
        font-family: EuroStileRegObl, sans-serif;
        font-size: 34px;
        transition: 200ms ease;

        &:hover {
            color: #f00!important;
        }

        &:after, &:before {
            content: '|';
        }
    }

    .background__logo {
        opacity: .45;
        margin-right: 100%;
    }

    ul {
        list-style: none;
        display: flex;
        justify-content: space-between;
        margin-top: 150px;

        li {
            a {
                display: flex;
                align-items: center;

                &:hover {
                    svg {
                        fill: #f00;
                    }

                    span {
                        color: #f00;
                    }
                }
                svg {
                    width: 25px;
                    height: 25px;
                    fill: #e8e8e8;
                    stroke-width: 1px;
                    transition: 200ms ease;
                }

                span {
                    font-size: 25px;
                    color: #fff;
                    font-family: EuroStileRegObl, sans-serif;
                    text-transform: uppercase;
                    margin-left: 10px;
                    transition: 200ms ease;
                }
            }
        }
    }
</style>
