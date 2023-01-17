import React, { useEffect, useState } from 'react'
import Spinner from '../includes/spinner/Spinner';
import SmallSpinner from '../includes/spinner/SmallSpinner'
import { Link, withRouter } from 'react-router-dom';
import * as Constants from "../../constants";
import { set, toInteger } from 'lodash';
const queryString = require('query-string');
import { withTranslation } from 'react-i18next';
import RangeSlider from 'react-bootstrap-range-slider';
import 'bootstrap/dist/css/bootstrap.css'; // or include from a CDN
import 'react-bootstrap-range-slider/dist/react-bootstrap-range-slider.css';
import { Form, Row, Col } from "react-bootstrap";

var total = 0.0;
var etotal = 0;
var stotal = 0;
var ctotal = 0;
function Packages(props) {
    const { t } = props;
    const [loading, setLoading] = useState(false);
    const [packages, setPackages] = useState([]);
    const [package_subscription, setPackage_subscription] = useState({});
    const [selected_package, setSelected_package] = useState({});
    const [profile, setProfile] = useState({});
    const [type, setType] = useState(1);
    const [countryVatLoading, setCountryVatLoading] = useState(false);
    const [vat_rate, setVat_rate] = useState(null);
    const [canSwitch, setCanSwitch] = useState(true);
    const [section, setSection] = useState(1);
    const [emailvalue, setEmailValue] = React.useState(0);
    const [smsvalue, setSmsValue] = React.useState(0);
    const [contactvalue, setContactValue] = React.useState(0);


    // const [espan1_start, setEspan1_start] = React.useState(0);
    const [espan1_end, setEspan1_end] = React.useState(0);
    const [espan1_price, setEspan1_price] = React.useState(0);
    // const [espan2_start, setEspan2_start] = React.useState(0);
    const [espan2_end, setEspan2_end] = React.useState(0);
    const [espan2_price, setEspan2_price] = React.useState(0);
    // const [espan3_start, setEspan3_start] = React.useState(0);
    const [espan3_price, setEspan3_price] = React.useState(0);

    // const [sspan1_start, setSspan1_start] = React.useState(0);
    const [sspan1_end, setSspan1_end] = React.useState(0);
    const [sspan1_price, setSspan1_price] = React.useState(0);
    // const [sspan2_start, setSspan2_start] = React.useState(0);
    const [sspan2_end, setSspan2_end] = React.useState(0);
    const [sspan2_price, setSspan2_price] = React.useState(0);
    // const [sspan3_start, setSspan3_start] = React.useState(0);
    const [sspan3_price, setSspan3_price] = React.useState(0);

    // const [cspan1_start, setCspan1_start] = React.useState(0);
    const [cspan1_end, setCspan1_end] = React.useState(0);
    const [cspan1_price, setCspan1_price] = React.useState(0);
    // const [cspan2_start, setCspan2_start] = React.useState(0);
    const [cspan2_end, setCspan2_end] = React.useState(0);
    const [cspan2_price, setCspan2_price] = React.useState(0);
    // const [cspan3_start, setCspan3_start] = React.useState(0);
    const [cspan3_price, setCspan3_price] = React.useState(0);


    useEffect(() => {
        const load = () => {
            setLoading(true)

            if (localStorage.jwt_token) {
                setLoading(true)

                axios.get('/api/auth/profile?lang=' + localStorage.lang).then(response => {
                    setProfile(response.data.data);
                    setVat_rate(response.data.vat_rate);
                });

                // axios.get('/api/subscription/check-status?lang=' + localStorage.lang).then(response => {
                //     // console.log(response)
                // });

                axios.get('/api/subscription/get-current-package?lang=' + localStorage.lang).then(response => {
                    setPackage_subscription(response.data.data.package_subscription);
                    if (response.data.data.package_subscription.package.id == 9) {
                        axios.get('/api/can-switch?lang=' + localStorage.lang).then(response => {
                            if (response.data.status == 0)
                                setCanSwitch(false);
                            else
                                setCanSwitch(true);
                        });
                    }
                });

                axios.get('/api/pay-as-you-go-pricing?lang=' + localStorage.lang).then(response => {
                    var pricedata = response.data.data;
                    // setEspan1_start(parseInt(pricedata.email_span1_start))
                    setEspan1_end(parseInt(pricedata.email_span1_end))
                    setEspan1_price(parseFloat(pricedata.email_span1_price))
                    // setEspan2_start(parseInt(pricedata.email_span2_start))
                    setEspan2_end(parseInt(pricedata.email_span2_end))
                    setEspan2_price(parseFloat(pricedata.email_span2_price))
                    // setEspan3_start(parseInt(pricedata.email_span3_start))
                    setEspan3_price(parseInt(pricedata.email_span3_price))

                    // setSspan1_start(parseInt(pricedata.sms_span1_start))
                    setSspan1_end(parseInt(pricedata.sms_span1_end))
                    setSspan1_price(parseFloat(pricedata.sms_span1_price))
                    // setSspan2_start(parseInt(pricedata.sms_span2_start))
                    setSspan2_end(parseInt(pricedata.sms_span2_end))
                    setSspan2_price(parseFloat(pricedata.sms_span2_price))
                    // setSspan3_start(parseInt(pricedata.sms_span3_start))
                    setSspan3_price(parseFloat(pricedata.sms_span3_price))

                    // setCspan1_start(parseInt(pricedata.contacts_span1_start))
                    setCspan1_end(parseInt(pricedata.contacts_span1_end))
                    setCspan1_price(parseFloat(pricedata.contacts_span1_price))
                    // setCspan2_start(parseInt(pricedata.contacts_span2_start))
                    setCspan2_end(parseInt(pricedata.contacts_span2_end))
                    setCspan2_price(parseFloat(pricedata.contacts_span2_price))
                    // setCspan3_start(parseInt(pricedata.contacts_span3_start))
                    setCspan3_price(parseFloat(pricedata.contacts_span3_price))
                });
            } else {
                // setCountryVatLoading(true);

                const state = this;
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function () {
                    if (readyState == 4 && status == 200) {
                        let result = JSON.parse(responseText);
                        axios.get('/api/get-country-vat?country_name=' + result.data.country_name + '&country_code=' + result.data.country_code + '&lang=' + localStorage.lang)
                            .then(response => {
                                setVat_rate(response.data.data.vat)
                                // setCountryVatLoading(false)
                            })
                    }
                };
                const url = '/api/get-geo-location';
                xhttp.open("GET", url, true);
                xhttp.send();
            }

            axios.get('/api/packages?lang=' + localStorage.lang)
                .then(response => {
                    setPackages(response.data.data)
                    setLoading(false)
                })
        }
        load();
    }, [])

    const handleTotal = (sliderVal, module) => {
        total = 0;
        sliderVal = parseInt(sliderVal);
        if (module == 'email') {
            if (sliderVal <= espan1_end)
                etotal = (sliderVal) * espan1_price;
            else if (sliderVal <= espan2_end)
                etotal = ((espan1_end) * espan1_price) + ((sliderVal - espan1_end)) * espan2_price;
            else
                etotal = ((espan1_end) * espan1_price) + (((espan2_end - espan1_end)) * espan2_price) + (((sliderVal - espan2_end)) * espan3_price);
        }
        if (module == 'sms') {
            if (sliderVal <= sspan1_end)
                stotal = (sliderVal) * sspan1_price;
            else if (sliderVal <= sspan2_end)
                stotal = ((sspan1_end) * sspan1_price) + ((sliderVal - sspan1_end)) * sspan2_price;
            else
                stotal = ((sspan1_end) * sspan1_price) + (((sspan2_end - sspan1_end)) * sspan2_price) + (((sliderVal - sspan2_end)) * sspan3_price);
        }
        if (module == 'contact') {
            if (sliderVal <= cspan1_end)
                ctotal = (sliderVal) * cspan1_price;
            else if (sliderVal <= cspan2_end)
                ctotal = ((cspan1_end) * cspan1_price) + ((sliderVal - cspan1_end)) * cspan2_price;
            else
                ctotal = ((cspan1_end) * cspan1_price) + (((cspan2_end - cspan1_end)) * cspan2_price) + (((sliderVal - cspan2_end)) * cspan3_price);
        }
        total = etotal + stotal + ctotal;
    }

    const handlePayNow = (event, package_id) => {
        event.preventDefault();

        if (package_subscription.package_id == 9) {
            console.log("Check Payments");
            axios.get('/api/pending-payments?lang=' + localStorage.lang).then(response => {
                if (response.data.status == 0) {
                    // no pending payment generated, continue
                    localStorage["type"] = type;
                    localStorage["package_id"] = package_id;
                    window.location.href = "/packages/payment-checkout";
                } else {
                    // pending payment generated, clear payments first
                    window.location.href = "/packages/payments";

                }
            });
        }
        else {
            localStorage["type"] = type;
            localStorage["package_id"] = package_id;
            window.location.href = "/packages/payment-checkout";
        }
        // if (localStorage.jwt_token) {
        // }
        // else {
        //     localStorage["on_hold_package_id"] = package_id;
        //     props.history.push('/signup')
        // }

    }

    const handlePackageTypeRadioClick = (event, value) => {
        setType(value)
    }

    const pay_as_you_go_section = packages.filter(pkg => pkg.id == 9).map(single_package => {
        return (
            <div className="col-12 single-package mt-5" key={single_package.id}>
                {loading ? <Spinner /> : null}
                {single_package.recommended ?
                    <div className='pay-go-label'>
                        <span className="recommended">{t('Recommended')}</span>
                    </div>
                    : null
                }
                <div className="generic_content">
                    <div className="generic_head_price">
                        <div className="generic_head_content">
                            <div className="head_bg"></div>
                            <div className="head pb-4 pt-3">
                                <span>{single_package.title}</span>
                            </div>
                        </div>
                    </div>
                    <div className="pay-go generic_feature_list mt-5 px-md-4 px-2">
                        <h2>{single_package.sub_title}</h2>
                        <p dangerouslySetInnerHTML={{ __html: single_package.description }} />
                        <Form>
                            <div className='d-flex flex-column px-md-5 px-2 justify-content-center align-items-center'>
                                <div className='d-flex justify-content-between align-items-center py-md-5 py-2 range-value'>
                                    <div className='d-flex justify-content-end align-items-center'>
                                        <h3><strong>{t('emails_cost')}: </strong></h3>
                                    </div>
                                    <div className='d-flex justify-content-start align-items-center'>
                                        <h2 className='m-0'> {vat_rate != null ? (etotal + ((etotal * Number(vat_rate) / 100))).toFixed(2) : etotal.toFixed(2)} <span className="sign">€</span> </h2>
                                    </div>
                                </div>
                                <div className='d-flex w-100 slider-style justify-content-center align-items-center'>
                                    <strong className='me-2'> {t('Email')}:</strong>
                                    <RangeSlider
                                        value={emailvalue}
                                        step={500}
                                        min={0}
                                        max={10000}
                                        onChange={e => { handleTotal(e.target.value, 'email'); setEmailValue(e.target.value); }}
                                    />
                                </div>
                                <div className='d-flex justify-content-between align-items-center py-sm-5 py-2 range-value'>
                                    <div className='d-flex justify-content-end align-items-center'>
                                        <h3><strong>{t('sms_cost')}: </strong></h3>
                                    </div>
                                    <div className='d-flex justify-content-start align-items-center'>
                                        <h2 className='m-0'> {vat_rate != null ? (stotal + ((stotal * Number(vat_rate) / 100))).toFixed(2) : stotal.toFixed(2)} <span className="sign">€</span> </h2>
                                    </div>
                                </div>
                                <div className='d-flex w-100 slider-style justify-content-center align-items-center'>
                                    <strong className='me-2'>{t('SMS')}:</strong>
                                    <RangeSlider
                                        value={smsvalue}
                                        step={500}
                                        min={0}
                                        max={10000}
                                        onChange={e => { setSmsValue(e.target.value); handleTotal(e.target.value, 'sms') }}
                                    />
                                </div>
                                {/* <div className='d-flex justify-content-between align-items-center py-sm-5 py-2 range-value'>
                                    <div className='d-flex justify-content-end align-items-center'>
                                        <h3><strong>{t('contacts_cost')}: </strong></h3>
                                    </div>
                                    <div className='d-flex justify-content-start align-items-center'>
                                        <h2 className='m-0'> {ctotal.toFixed(2)} <span className="sign">€</span> </h2>
                                    </div>
                                </div>
                                <div className='d-flex w-100 slider-style justify-content-center align-items-center'>
                                    <strong className='me-2'>{t('Contacts')}:</strong>
                                    <RangeSlider
                                        value={contactvalue}
                                        step={100}
                                        min={0}
                                        max={1500}
                                        onChange={e => { setContactValue(e.target.value); handleTotal(e.target.value, 'contact') }}
                                    />
                                </div> */}
                            </div>
                        </Form>
                    </div>
                    <div className="generic_price_btn">
                        {
                            Object.keys(package_subscription).length > 0 && package_subscription.package_id == single_package.id && (package_subscription.type == type || package_subscription.type == null) ? (
                                profile.is_expired == 1 && package_subscription.package_id != 2 ? (
                                    <Link to="#" className="btn btn-secondary" onClick={(event) => handlePayNow(event, single_package.id)}>
                                        <span> {t('expired')}</span>
                                    </Link>
                                ) : (
                                    <button type="button" disabled={true} className="btn btn-current">{t('Current Plan')}</button>
                                )
                            ) : (
                                canSwitch ?
                                    <Link to="#" className="btn btn-primary" onClick={(event) => handlePayNow(event, single_package.id)}>
                                        <span>
                                            {
                                                Object.keys(profile).length > 0 && profile.on_hold_package_id == single_package.id ? t('pay_now') : (
                                                    single_package.id == 2 || single_package.id == 9 ? t('subscribe') : t('pay_now')
                                                )
                                            }
                                        </span>
                                    </Link>
                                    :
                                    <Link to="/packages/payments">
                                        <button type="button" disabled={true} className="btn btn-current">{t('Clear payments First')}</button>
                                    </Link>
                            )
                        }
                    </div>
                </div>
            </div >
        )
    })

    const packages_list = packages.filter(pkg => pkg.id != 9).map(single_package => {
        return (
            single_package.id != 2 || type == 1 ?
                <div className="col-xl-4 col-sm-6 single-package mt-5" key={single_package.id}>
                    {loading ? <Spinner /> : null}
                    {single_package.recommended ?
                        <div className='pay-go-label'>
                            <span className="recommended">{t('Recommended')}</span>
                        </div>
                        : null
                    }
                    <div className="generic_content">
                        <div className="generic_head_price">
                            <div className="generic_head_content">
                                <div className="head_bg"></div>
                                <div className="head">
                                    <span>{single_package.title}</span>
                                </div>
                            </div>
                            <div className="generic_price_tag">
                                <span className="price">
                                    {
                                        type == 1 ? (
                                            <>
                                                <span className="sign">€</span>
                                                <span className="currency">
                                                    {vat_rate != null ? (single_package.monthly_price + ((single_package.monthly_price * Number(vat_rate) / 100))).toFixed(2) : single_package.monthly_price} / {t('month')}
                                                </span>
                                                <br></br>
                                            </>
                                        ) : (
                                            <>
                                                <span className="sign">€</span>
                                                <span className="currency">
                                                    {vat_rate != null ? (single_package.yearly_price + ((single_package.yearly_price * Number(vat_rate) / 100))).toFixed(2) : single_package.yearly_price} / {t('year')}
                                                </span>
                                                <br></br>
                                            </>
                                        )
                                    }
                                    <div className="tooltip">
                                        <span className="month">{single_package.sub_title}
                                            {/* <span className="tooltiptext">{single_package.sub_title}</span> */}
                                        </span>
                                    </div>
                                </span>
                            </div>
                        </div>
                        <div className="generic_feature_list">
                            <div dangerouslySetInnerHTML={{ __html: single_package.description }} />
                        </div>
                        <div className="generic_price_btn">
                            {
                                Object.keys(package_subscription).length > 0 && package_subscription.package_id == single_package.id && (package_subscription.type == type || package_subscription.type == null) ? (
                                    profile.is_expired == 1 && package_subscription.package_id != 2 ? (
                                        <Link to="#" className="btn btn-secondary" onClick={(event) => handlePayNow(event, single_package.id)}>
                                            <span> {t('expired')}</span>
                                        </Link>
                                    ) : (
                                        <button type="button" disabled={true} className="btn btn-current">{t('Current Plan')}</button>
                                    )
                                ) : (
                                    canSwitch ?
                                        <Link to="#" className="btn btn-primary" onClick={(event) => handlePayNow(event, single_package.id)}>
                                            <span>
                                                {
                                                    Object.keys(profile).length > 0 && profile.on_hold_package_id == single_package.id ? t('pay_now') : (
                                                        single_package.id == 2 || single_package.id == 9 ? t('free') : t('pay_now')
                                                    )
                                                }
                                            </span>
                                        </Link>
                                        :
                                        <Link to="/packages/payments">
                                            <button type="button" disabled={true} className="btn btn-current">{t('Clear payments First')}</button>
                                        </Link>
                                )
                            }
                        </div>
                    </div>
                </div >
                : ""
        )
    })

    return (
        <>
            <section className="up-packages pricing-wrapper">
                {loading || countryVatLoading ? <Spinner /> : null}
                <div className="pricing-packages">
                    <div className="section-heading">
                        <h2>{t('PACKAGE PRICING STRUCTURES')}</h2>
                    </div>
                    <div className="pricing-toggle mt-sm-0 mt-2">
                        <label className="custom-radio price-toggle">
                            <input type="radio" name="radio" defaultChecked onClick={(event) => { handlePackageTypeRadioClick(event, 1); setSection(1) }} />
                            <p>{t('Monthly')}</p>
                            <span className="checkmark"></span>
                        </label>
                        <label className="custom-radio price-toggle mt-sm-0 mt-2">
                            <input type="radio" name="radio" onClick={(event) => { handlePackageTypeRadioClick(event, 2); setSection(1) }} />
                            <p>{t('annually')}</p>
                            <span className="checkmark"></span>
                        </label>
                        <label className="custom-radio price-toggle mt-sm-0 mt-2">
                            <input type="radio" name="radio" onClick={(event) => { handlePackageTypeRadioClick(event, 1); setSection(2) }} />
                            <p>{t('payg')}</p>
                            <span className="checkmark"></span>
                        </label>
                    </div>
                    <div className="container-fluid">
                        <div className="row">
                            {section == 1 ?
                                packages_list
                                :
                                <>
                                    {pay_as_you_go_section}
                                </>
                            }
                        </div>
                    </div>
                </div>
            </section>
        </>
    )
}

export default withTranslation()(Packages)
