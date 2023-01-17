import React, { useState } from 'react'
import { Link } from "react-router-dom"
import { useTranslation } from 'react-i18next'


const LanguageSelector = () => {
    const { t, i18n } = useTranslation()
    const [lang, setLang] = useState('en')

    const changeLanguage = async (val) => {
        i18n.changeLanguage(val)
        setLang(val)
        localStorage['lang'] = val
        if (localStorage.jwt_token)
            await axios.get("/api/auth/update-user-lang?lang=" + val)
        location.reload()
    }

    return (
        <div className="nav-lang-drop">
            <div className="dropdown lang-drop">
                <button className="dropdown-toggle p-0" type="button" id="dropdownLangButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src={`/images/flag/${localStorage.lang}.svg`} className="img-responsive" alt="" />
                </button>
                <div className="dropdown-menu lang-dropdown-menu" aria-labelledby="dropdownLangButton">
                    <Link className={localStorage.lang == 'bg' ? "dropdown-item flag_link taj active" : "dropdown-item flag_link taj"} to="#" data-lang="Bulgarian" title="Bulgarian" onClick={() => changeLanguage('bg')}>
                        <span className="lang-name">Bulgarian</span>
                        <img src="/images/flag/bg.svg" className="img-responsive" alt="" /></Link>
                    <Link className={localStorage.lang == 'zh' ? "dropdown-item flag_link taj active" : "dropdown-item flag_link taj"} to="#" data-lang="Chinese (simplified)" title="Chinese (simplified)" onClick={() => changeLanguage('zh')}>
                        <span className="lang-name">Chinese (simplified)</span>
                        <img src="/images/flag/zh.svg" className="img-responsive" alt="" /></Link>
                    <Link className={localStorage.lang == 'cs' ? "dropdown-item flag_link taj active" : "dropdown-item flag_link taj"} to="#" data-lang="Czech" title="Czech" onClick={() => changeLanguage('cs')}>
                        <span className="lang-name">Czech</span>
                        <img src="/images/flag/cs.svg" className="img-responsive" alt="" /></Link>
                    <Link className={localStorage.lang == 'da' ? "dropdown-item flag_link taj active" : "dropdown-item flag_link taj"} to="#" data-lang="Danish" title="Danish" onClick={() => changeLanguage('da')}>
                        <span className="lang-name">Danish</span>
                        <img src="/images/flag/da.svg" className="img-responsive" alt="" /></Link>
                    <Link className={localStorage.lang == 'nl' ? "dropdown-item flag_link taj active" : "dropdown-item flag_link taj"} to="#" data-lang="Dutch" title="Dutch" onClick={() => changeLanguage('nl')}>
                        <span className="lang-name">Dutch</span>
                        <img src="/images/flag/nl.svg" className="img-responsive" alt="" /></Link>
                    <Link className={localStorage.lang == 'en' ? "dropdown-item flag_link eng active" : "dropdown-item flag_link taj"} to="#" data-lang="English" title="English" onClick={() => changeLanguage('en')}>
                        <span className="lang-name">English</span>
                        <img src="/images/flag/en.svg" className="img-responsive" alt="" /></Link>
                    <Link className={localStorage.lang == 'et' ? "dropdown-item flag_link taj active" : "dropdown-item flag_link taj"} to="#" data-lang="Estonian" title="Estonian" onClick={() => changeLanguage('et')}>
                        <span className="lang-name">Estonian</span>
                        <img src="/images/flag/et.svg" className="img-responsive" alt="" /></Link>
                    <Link className={localStorage.lang == 'fi' ? "dropdown-item flag_link taj active" : "dropdown-item flag_link taj"} to="#" data-lang="Finnish" title="Finnish" onClick={() => changeLanguage('fi')}>
                        <span className="lang-name">Finnish</span>
                        <img src="/images/flag/fi.svg" className="img-responsive" alt="" /></Link>
                    <Link className={localStorage.lang == 'fr' ? "dropdown-item flag_link taj active" : "dropdown-item flag_link taj"} to="#" data-lang="French" title="French" onClick={() => changeLanguage('fr')}>
                        <span className="lang-name">French</span>
                        <img src="/images/flag/fr.svg" className="img-responsive" alt="" /></Link>
                    <Link className={localStorage.lang == 'de' ? "dropdown-item flag_link taj active" : "dropdown-item flag_link taj"} to="#" data-lang="German" title="German" onClick={() => changeLanguage('de')}>
                        <span className="lang-name">German</span>
                        <img src="/images/flag/de.svg" className="img-responsive" alt="" /></Link>
                    <Link className={localStorage.lang == 'el' ? "dropdown-item flag_link taj active" : "dropdown-item flag_link taj"} to="#" data-lang="Greek" title="Greek" onClick={() => changeLanguage('el')}>
                        <span className="lang-name">Greek</span>
                        <img src="/images/flag/el.svg" className="img-responsive" alt="" /></Link>
                    <Link className={localStorage.lang == 'hu' ? "dropdown-item flag_link taj active" : "dropdown-item flag_link taj"} to="#" data-lang="Hungarian" title="Hungarian" onClick={() => changeLanguage('hu')}>
                        <span className="lang-name">Hungarian</span>
                        <img src="/images/flag/hu.svg" className="img-responsive" alt="" /></Link>
                    <Link className={localStorage.lang == 'it' ? "dropdown-item flag_link taj active" : "dropdown-item flag_link taj"} to="#" data-lang="Italian" title="Italian" onClick={() => changeLanguage('it')}>
                        <span className="lang-name">Italian</span>
                        <img src="/images/flag/it.svg" className="img-responsive" alt="" /></Link>
                    <Link className={localStorage.lang == 'ja' ? "dropdown-item flag_link taj active" : "dropdown-item flag_link taj"} to="#" data-lang="Japanese" title="Japanese" onClick={() => changeLanguage('ja')}>
                        <span className="lang-name">Japanese</span>
                        <img src="/images/flag/ja.svg" className="img-responsive" alt="" /></Link>
                    <Link className={localStorage.lang == 'lv' ? "dropdown-item flag_link taj active" : "dropdown-item flag_link taj"} to="#" data-lang="Latvian" title="Latvian" onClick={() => changeLanguage('lv')}>
                        <span className="lang-name">Latvian</span>
                        <img src="/images/flag/lv.svg" className="img-responsive" alt="" /></Link>
                    <Link className={localStorage.lang == 'lt' ? "dropdown-item flag_link taj active" : "dropdown-item flag_link taj"} to="#" data-lang="Lithuanian" title="Lithuanian" onClick={() => changeLanguage('lt')}>
                        <span className="lang-name">Lithuanian</span>
                        <img src="/images/flag/lt.svg" className="img-responsive" alt="" /></Link>
                    <Link className={localStorage.lang == 'pl' ? "dropdown-item flag_link taj active" : "dropdown-item flag_link taj"} to="#" data-lang="Polish" title="Polish" onClick={() => changeLanguage('pl')}>
                        <span className="lang-name">Polish</span>
                        <img src="/images/flag/pl.svg" className="img-responsive" alt="" /></Link>
                    <Link className={localStorage.lang == 'br' ? "dropdown-item flag_link taj active" : "dropdown-item flag_link taj"} to="#" data-lang="Portuguese - Brazil" title="Portuguese - Brazil" onClick={() => changeLanguage('br')}>
                        <span className="lang-name">Portuguese - Brazil</span>
                        <img src="/images/flag/br.svg" className="img-responsive" alt="" /></Link>
                    <Link className={localStorage.lang == 'pt' ? "dropdown-item flag_link taj active" : "dropdown-item flag_link taj"} to="#" data-lang="Portuguese - Portugal" title="Portuguese - Portugal" onClick={() => changeLanguage('pt')}>
                        <span className="lang-name">Portuguese - Portugal</span>
                        <img src="/images/flag/pt.svg" className="img-responsive" alt="" /></Link>
                    <Link className={localStorage.lang == 'ro' ? "dropdown-item flag_link taj active" : "dropdown-item flag_link taj"} to="#" data-lang="Romanian" title="Romanian" onClick={() => changeLanguage('ro')}>
                        <span className="lang-name">Romanian</span>
                        <img src="/images/flag/ro.svg" className="img-responsive" alt="" /></Link>
                    <Link className={localStorage.lang == 'ru' ? "dropdown-item flag_link taj active" : "dropdown-item flag_link taj"} to="#" data-lang="Russian" title="Russian" onClick={() => changeLanguage('ru')}>
                        <span className="lang-name">Russian</span>
                        <img src="/images/flag/ru.svg" className="img-responsive" alt="" /></Link>
                    <Link className={localStorage.lang == 'sk' ? "dropdown-item flag_link taj active" : "dropdown-item flag_link taj"} to="#" data-lang="Slovak" title="Slovak" onClick={() => changeLanguage('sk')}>
                        <span className="lang-name">Slovak</span>
                        <img src="/images/flag/sk.svg" className="img-responsive" alt="" /></Link>
                    <Link className={localStorage.lang == 'sl' ? "dropdown-item flag_link taj active" : "dropdown-item flag_link taj"} to="#" data-lang="Slovenian" title="Slovenian" onClick={() => changeLanguage('sl')}>
                        <span className="lang-name">Slovenian</span>
                        <img src="/images/flag/sl.svg" className="img-responsive" alt="" /></Link>
                    <Link className={localStorage.lang == 'es' ? "dropdown-item flag_link taj active" : "dropdown-item flag_link taj"} to="#" data-lang="Spanish" title="Spanish" onClick={() => changeLanguage('es')}>
                        <span className="lang-name">Spanish</span>
                        <img src="/images/flag/es.svg" className="img-responsive" alt="" /></Link>
                    <Link className={localStorage.lang == 'sv' ? "dropdown-item flag_link taj active" : "dropdown-item flag_link taj"} to="#" data-lang="Swedish" title="Swedish" onClick={() => changeLanguage('sv')}>
                        <span className="lang-name">Swedish</span>
                        <img src="/images/flag/sv.svg" className="img-responsive" alt="" /></Link>
                </div>
            </div>
        </div>
    )
}

export default LanguageSelector