<template>
    <v-card :style="cardStyles">
        <v-card-title></v-card-title>
        <v-card-text>
            <div class="container">
                <img :src="require('./../../../assets/images/logo-color.png')" alt="" class="background__logo">
                <h1>Спасибо за правильный выбор в пользу нашего магазина!</h1>
                <h3>Будем рады сотрудничеству с вашими тренерами</h3>
                <a href="https://iron-addicts.kz/trainer" class="main__link" target="_blank">
                    www.iron-addicts.kz/trainer
                </a>
                <ul>
                    <li>
                        <a href="https://iron-addicts.kz" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 508 508">
                                <path d="M254 0C146.7 0 0 81.1 0 254c0 168.5 141.1 254 254 254 193.7 0 254-169.7 254-254C508 129.6 412.8 0 254 0zm-58.9 23.9c-26.5 22.6-48.5 60-62.7 106.4-18.4-10.9-35.3-24.4-50.3-40.1 31-32.5 70.2-55.3 113-66.3zM71.2 102.4c16.8 17.5 35.9 32.4 56.7 44.2-7.8 30.3-12.4 63.9-13 99.2H16.6c1.8-52.7 21-103 54.6-143.4zm0 303.2c-33.7-40.4-52.8-90.7-54.6-143.4h98.3c.6 35.4 5.2 68.9 13 99.2-20.7 11.9-39.8 26.7-56.7 44.2zm10.9 12.3c15-15.7 31.9-29.2 50.3-40.1 14.2 46.3 36.2 83.8 62.7 106.4-42.8-11.1-82-33.9-113-66.3zM245.8 491c-42.6-5.4-79.3-53-99.1-121.2 30.6-15.5 64.4-24.2 99.1-25.5V491zm0-163c-36.2 1.2-71.4 10.1-103.3 25.7-6.7-28-10.7-58.9-11.3-91.5h114.6V328zm0-82.2H131.2c.6-32.6 4.6-63.5 11.3-91.5 32 15.6 67.2 24.5 103.3 25.7v65.8zm0-82.1c-34.8-1.2-68.5-10-99.1-25.5C166.5 69.9 203.2 22.4 245.8 17v146.7zm191-61.3c33.6 40.4 52.8 90.7 54.6 143.4h-98.2c-.6-35.4-5.2-68.9-13-99.2 20.7-11.9 39.8-26.7 56.6-44.2zm-10.9-12.3c-15 15.7-31.9 29.2-50.3 40.1-14.2-46.3-36.2-83.7-62.7-106.4 42.8 11.1 82 33.9 113 66.3zM262.2 17c42.6 5.4 79.3 53 99.1 121.2-30.6 15.5-64.3 24.2-99.1 25.5V17zm0 163c36.2-1.2 71.4-10.1 103.3-25.7 6.7 28 10.7 58.9 11.3 91.5H262.2V180zm0 82.2h114.6c-.6 32.6-4.6 63.5-11.3 91.5-31.9-15.7-67.1-24.6-103.3-25.7v-65.8zm0 228.8V344.3c34.8 1.2 68.5 10 99.1 25.5-19.8 68.3-56.5 115.8-99.1 121.2zm50.7-6.9c26.5-22.6 48.5-60 62.7-106.4 18.4 10.9 35.3 24.4 50.3 40.1-31 32.5-70.2 55.3-113 66.3zm123.9-78.5c-16.8-17.5-35.9-32.3-56.6-44.2 7.8-30.3 12.4-63.9 13-99.2h98.2c-1.8 52.7-21 103-54.6 143.4z"/>
                            </svg>
                            <span>
                            iron-addicts.kz
                        </span>
                        </a>
                    </li>
                    <li>
                        <a href="https://www.instagram.com/iron_addicts_pvl/" target="_blank">
                            <svg height="32" viewBox="0 0 24 24" width="32" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.004 5.838a6.157 6.157 0 00-6.158 6.158 6.157 6.157 0 006.158 6.158 6.157 6.157 0 006.158-6.158 6.157 6.157 0 00-6.158-6.158zm0 10.155a3.996 3.996 0 113.997-3.997 3.995 3.995 0 01-3.997 3.997z"/>
                                <path d="M16.948.076c-2.208-.103-7.677-.098-9.887 0-1.942.091-3.655.56-5.036 1.941C-.283 4.325.012 7.435.012 11.996c0 4.668-.26 7.706 2.013 9.979 2.317 2.316 5.472 2.013 9.979 2.013 4.624 0 6.22.003 7.855-.63 2.223-.863 3.901-2.85 4.065-6.419.104-2.209.098-7.677 0-9.887-.198-4.213-2.459-6.768-6.976-6.976zm3.495 20.372c-1.513 1.513-3.612 1.378-8.468 1.378-5 0-7.005.074-8.468-1.393-1.685-1.677-1.38-4.37-1.38-8.453 0-5.525-.567-9.504 4.978-9.788 1.274-.045 1.649-.06 4.856-.06l.045.03c5.329 0 9.51-.558 9.761 4.986.057 1.265.07 1.645.07 4.847-.001 4.942.093 6.959-1.394 8.453z"/>
                                <circle cx="18.406" cy="5.595" r="1.439"/>
                            </svg>
                            <span>iron_addicts_pvl</span>
                        </a>
                    </li>
                    <li>
                        <a href="https://api.WhatsApp.com/send?phone=77053000227" target="_blank">
                            <svg height="32" viewBox="0 0 24 24" width="32" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.507 14.307l-.009.075c-2.199-1.096-2.429-1.242-2.713-.816-.197.295-.771.964-.944 1.162-.175.195-.349.21-.646.075-.3-.15-1.263-.465-2.403-1.485-.888-.795-1.484-1.77-1.66-2.07-.293-.506.32-.578.878-1.634.1-.21.049-.375-.025-.524-.075-.15-.672-1.62-.922-2.206-.24-.584-.487-.51-.672-.51-.576-.05-.997-.042-1.368.344-1.614 1.774-1.207 3.604.174 5.55 2.714 3.552 4.16 4.206 6.804 5.114.714.227 1.365.195 1.88.121.574-.091 1.767-.721 2.016-1.426.255-.705.255-1.29.18-1.425-.074-.135-.27-.21-.57-.345z"/>
                                <path d="M20.52 3.449C12.831-3.984.106 1.407.101 11.893c0 2.096.549 4.14 1.595 5.945L0 24l6.335-1.652c7.905 4.27 17.661-1.4 17.665-10.449 0-3.176-1.24-6.165-3.495-8.411zm1.482 8.417c-.006 7.633-8.385 12.4-15.012 8.504l-.36-.214-3.75.975 1.005-3.645-.239-.375c-4.124-6.565.614-15.145 8.426-15.145a9.865 9.865 0 017.021 2.91 9.788 9.788 0 012.909 6.99z"/>
                            </svg>
                            <span>
                            +7 705 3000 227
                        </span>
                        </a>
                    </li>
                </ul>
            </div>
        </v-card-text>
    </v-card>
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
