import i18n from 'i18next'
import Backend from 'i18next-xhr-backend'
import { initReactI18next } from 'react-i18next'

function loadUserNativeLang() {
    localStorage["lang"] = 'en';
    // const that = this;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            let result = JSON.parse(this.responseText);
            if (result.data.country == "Germany") {
                localStorage["lang"] = 'de';
            } else if (result.data.country == "France") {
                localStorage["lang"] = 'fr';
            } else if (result.data.country == "Spain") {
                localStorage["lang"] = 'es';
            } else if (result.data.country == "Brazil") {
                localStorage["lang"] = 'br';
            } else if (result.data.country == "Portugal") {
                localStorage["lang"] = 'pt';
            } else if (result.data.country == "Italy") {
                localStorage["lang"] = 'it';
            } else if (result.data.country == "Netherlands") {
                localStorage["lang"] = 'nl';
            } else if (result.data.country == "Poland") {
                localStorage["lang"] = 'pl';
            } else if (result.data.country == "Russia" || result.data.country == "Russian Federation") {
                localStorage["lang"] = 'ru';
            } else if (result.data.country == "Japan") {
                localStorage["lang"] = 'ja';
            } else if (result.data.country == "China") {
                localStorage["lang"] = 'zh';
            } else if (result.data.country == "Bulgaria") {
                localStorage["lang"] = 'bg';
            } else if (result.data.country == "Czechia" || result.data.country == "Czech Republic") {
                localStorage["lang"] = 'cs';
            } else if (result.data.country == "Denmark") {
                localStorage["lang"] = 'da';
            } else if (result.data.country == "Estonia") {
                localStorage["lang"] = 'et';
            } else if (result.data.country == "Finland") {
                localStorage["lang"] = 'fi';
            } else if (result.data.country == "Greece") {
                localStorage["lang"] = 'el';
            } else if (result.data.country == "Hungary") {
                localStorage["lang"] = 'hu';
            } else if (result.data.country == "Latvia") {
                localStorage["lang"] = 'lv';
            } else if (result.data.country == "Lithuania") {
                localStorage["lang"] = 'lt';
            } else if (result.data.country == "Romania") {
                localStorage["lang"] = 'ro';
            } else if (result.data.country == "Slovakia") {
                localStorage["lang"] = 'sk';
            } else if (result.data.country == "Slovenia") {
                localStorage["lang"] = 'sl';
            } else if (result.data.country == "Sweden") {
                localStorage["lang"] = 'sv';
            }
            if (localStorage.lang != "en") {
                window.location.reload();
            }
        }
    };
    const url = '/api/get-geo-location';
    xhttp.open("GET", url, true);
    xhttp.send();
}


if (!localStorage.lang || localStorage.lang == null) {

    loadUserNativeLang();
}

$('body').addClass(localStorage.lang);

i18n
    .use(Backend)
    .use(initReactI18next)
    .init({
        lng: localStorage.lang,
        backend: {
            /* translation file path */
            loadPath: '/i18n/{{ns}}/{{lng}}.json'
        },
        fallbackLng: localStorage.lang,
        debug: true,
        /* can have multiple namespace, in case you want to divide a huge translation into smaller pieces and load them on demand */
        ns: ['translations'],
        defaultNS: 'translations',
        keySeparator: ".",
        interpolation: {
            escapeValue: false,
            formatSeparator: ','
        },
        react: {
            wait: true,
            useSuspense: false
        }
    })

export default i18n