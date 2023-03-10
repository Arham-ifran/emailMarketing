import React, { useEffect, useState } from 'react'
import { Link, withRouter } from 'react-router-dom';
import swal from 'sweetalert2';
import renderSnippet from '../helpers/renderSnippet';
import Spinner from '../includes/spinner/Spinner';
import SmallSpinner from '../includes/spinner/SmallSpinner'
import * as Constants from "../../constants";
import Swal from 'sweetalert2';
const queryString = require('query-string');
import { withTranslation } from 'react-i18next';
function PaymentCheckout(props) {

    const { t } = props;

    const [loading, setLoading] = useState(false);
    const [package_id, setPackage_id] = useState(2);
    const [type, setType] = useState(1);
    const [repetition, setRepetition] = useState(1);
    const [price, setPrice] = useState(0);
    const [discount_amount, setDiscount_amount] = useState(0);
    const [discount_percentage, setDiscount_percentage] = useState(0);
    const [voucher_apply, setVoucher_apply] = useState(false);
    const [voucher, setVoucher] = useState('');
    const [profile, setProfile] = useState({});
    const [selected_package, setSelected_package] = useState({});
    const [accountSettings, setAccountSettings] = useState({});
    const [payment_gateway_settings, setPayment_gateway_settings] = useState({});
    const [payment_method, setPayment_method] = useState('');
    const [vat_amount, setVat_amount] = useState(0);
    const [vat_percentage, setVat_percentage] = useState(0);
    const [showCardModal, setShowCardModal] = useState(0);
    const [errors, setErrors] = useState([]);
    const [disabledVoucherApplyBtn, setDisabledVoucherApplyBtn] = useState(0);

    useEffect(() => {
        const load = () => {
            $('html, body').animate({ scrollTop: 0 }, 0);

            const parsed = queryString.parse(props.location.search, {});
            console.log(parsed.package_id && parsed.type && parsed.repetition);
            if (parsed.package_id && parsed.type && parsed.repetition) {
                localStorage["package_id"] = Number(parsed.package_id);
                localStorage["type"] = Number(parsed.type);
                localStorage["repetition"] = Number(parsed.repetition);
                setPackage_id(localStorage.package_id)
                setType(localStorage.type)
                setRepetition(localStorage.repetition);
            }
            else {
                setPackage_id(localStorage.package_id)
                setType(localStorage.type)
                setRepetition(1);
                localStorage["repetition"] = 1;
            }

            if (localStorage.package_id) {
                if (localStorage.package_id == 2 || localStorage.package_id == 9) {
                    setPackage_id(localStorage.package_id)
                    setType(1)
                    handleConfirmPayment()
                }
                else {
                    setLoading(true)

                    axios.get(Constants.BASE_URL + '/api/auth/profile?lang=' + localStorage.lang).then(response => {
                        setProfile(response.data.data)
                        setVoucher(response.data.data.voucher)
                    });

                    axios.get(Constants.BASE_URL + '/api/payment-gateway-settings?lang=' + localStorage.lang).then(response => {
                        setPayment_gateway_settings(response.data.data)
                    });

                    axios.get(Constants.BASE_URL + '/api/package-detail?package_id=' + localStorage.package_id + '&lang=' + localStorage.lang)
                        .then(response => {
                            if (response.data.data) {
                                let selected_package = response.data.data;
                                let vat_percentage = response.data.additional.vat;
                                let price = (localStorage.type == 1) ? selected_package.monthly_price * localStorage.repetition : selected_package.yearly_price * localStorage.repetition;
                                let vat_amount = (price * vat_percentage) / 100;
                                vat_amount = vat_amount.toFixed(2);
                                console.log("rep: ");
                                console.log(localStorage.repetition);
                                setPackage_id(localStorage.package_id)
                                setType(localStorage.type)
                                setPrice(price)
                                setSelected_package(selected_package)
                                setVat_amount(vat_amount)
                                setVat_percentage(vat_percentage)
                                setLoading(false)
                            }
                            else {
                                window.location.href = "/packages/upgrade-package";
                                // props.history.push('/packages/upgrade-package');
                            }
                        })
                        .catch(error => {
                            // console.log(error)
                        })
                }
            }
            else {
                window.location.href = "/packages/upgrade-package";
                // props.history.push('/packages/upgrade-package');
            }
        }
        load();
    }, [])

    const hasErrorFor = (field) => {
        return !!errors[field]
    }

    const renderErrorFor = (field) => {
        if (hasErrorFor(field)) {
            return (
                <span className='invalid-feedback'>
                    <strong>{errors[field][0]}</strong>
                </span>
            )
        }
    }

    const handlePaymentMethod = (event, payment_method) => {
        event.preventDefault();
        const { t } = props;
        setLoading(true)
        setPayment_method(payment_method)
        const call = () => {
            if (payment_method == Constants.PAYMENT_METHODS.MOLLIE) {
                handleConfirmPayment(payment_method);
            } else {
                renderSnippet('<div></div>', 'checkout-container');
                axios.get(Constants.BASE_URL + '/api/auth/account-settings?lang=' + localStorage.lang).then(response => {
                    let accountSettings = response.data.data;

                    if (accountSettings && accountSettings.card_holder_name && accountSettings.card_brand && accountSettings.expire_month && accountSettings.expire_year && accountSettings.cvc) {
                        setShowCardModal(true)
                        setLoading(false)
                        setAccountSettings(accountSettings)
                        $('body').addClass('modal-open')
                    }
                    else {
                        setLoading(false)
                        $('body').removeClass('modal-open')
                        Swal.fire({
                            title: 'Ooops',
                            text: response.data.message,
                            icon: 'error',
                            showCancelButton: false,
                            confirmButtonText: t('OK'),
                        }).then((result) => {
                            window.location.href = "/packages/billing";
                        })
                    }
                });
            }
        }
        call();
    }

    const closeCardModal = () => {
        setShowCardModal(false)
        $('body').removeClass('modal-open')
    }

    const handleClickHere = (event) => {
        event.preventDefault()
        $('body').removeClass('modal-open')
        window.location.href = "/settings";
        // props.history.push('/settings')
    }

    const handleConfirmPayment = (payment_method) => {
        setLoading(true)

        const data = {
            package_id: localStorage.package_id,
            payment_method: payment_method,
            type: type,
            voucher: voucher,
            discount_percentage: Number(discount_percentage),
            discount_amount: discount_amount,
            repetition: localStorage.repetition,
        }

        axios.post(Constants.BASE_URL + '/api/subscription/payment-checkout?lang=' + localStorage.lang, data)
            .then(response => {
                const { t } = props;
                if (response.data.status) {

                    if (payment_method == Constants.PAYMENT_METHODS.MOLLIE) {
                        window.location.href = response.data.redirect_url;
                    } else {
                        setLoading(false)
                        setShowCardModal(false)
                        // $('body').removeClass('modal-open');
                        localStorage.removeItem('package_id');
                        localStorage.removeItem('type');
                        Swal.fire({
                            title: t('Success'),
                            text: t("Package Updated Successfully!"),
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonText: t('OK'),
                        }).then((result) => {
                            window.location.href = "/packages/billing";
                        })
                        // if (localStorage.direct_login_from_mobile == 1) {
                        //     props.history.push(`/packages/upgrade-package?user_id=${parseInt(localStorage.user_id)}&direct_login=1&lang=${localStorage.lang}&exit=1`);
                        // }
                        // else {
                        // window.location.href = "/packages/billing";
                        // props.history.push('/');
                        // }
                    }
                }
                else {
                    setLoading(false)
                    Swal.fire({
                        title: 'Oops',
                        text: response.data.message,
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonText: t('OK'),
                    })
                    // props.history.push('/packages/payment-checkout');
                }
            })
            .catch(error => {
                // console.log(error)
            })
    }

    const [state, setState] = useState({ voucher: "" });
    const handleFieldChange = (event) => {
        setVoucher(event.target.value)
        setDiscount_amount(0)
        setDiscount_percentage(0)
        setVoucher_apply(false)
    }

    const handleVoucherSubmit = (event) => {
        event.preventDefault()
        const { t } = props;
        setErrors([]);

        // console.log(voucher != undefined && voucher != NULL);
        if (voucher) {
            setLoading(true)
            setDisabledVoucherApplyBtn(true)

            const data = {
                voucher: voucher,
                platform: 'EMK',
                apply_voucher: false
            }

            axios.post(Constants.BASE_URL + '/api/vouchers/redeem?lang=' + localStorage.lang, data)
                .then((res) => {
                    if (res.data.status && res.data.status == 1) {
                        console.log("status 1");
                        let data = res.data.data;
                        // let errors = errors;
                        // delete errors["voucher"];

                        /*
                        ** Calculate Discount
                        */

                        let discount_percentage = data.order.discount_percentage;
                        let discount_amount = (price * discount_percentage) / 100;
                        discount_amount = discount_amount.toFixed(2);

                        setDiscount_amount(discount_amount)
                        setDiscount_percentage(discount_percentage)
                        // setErrors(errors)
                        setVoucher_apply(true)
                        setLoading(false)
                        setDisabledVoucherApplyBtn(false)

                        Swal.fire({
                            title: t('Success'),
                            text: t('Voucher has been applied!'),
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonText: t('OK'),
                        })
                    }
                    else {
                        console.log("status 0");
                        setDiscount_amount(0)
                        setDiscount_percentage(0)
                        setErrors({ voucher: [res.data.message] })
                        setVoucher_apply(false)
                        setLoading(false)
                        setDisabledVoucherApplyBtn(false)
                    }
                })
                .catch((error) => {
                    console.log("Err");
                    // if (error) {
                    //     setDiscount_amount(0)
                    //     setDiscount_percentage(0)
                    //     setErrors({ voucher: [t('voucher_is_not_valid')] })
                    //     setVoucher_apply(false)
                    //     setLoading(false)
                    //     setDisabledVoucherApplyBtn(false)
                    // }
                })
        }
        else {
            Swal.fire({
                title: 'Oops',
                text: t('Voucher cannot be empty'),
                icon: 'error',
                showCancelButton: false,
                confirmButtonText: t('OK'),
            })
        }
    }

    return (
        <>
            {loading ? <Spinner /> : null}
            <div className="main-content">
                {loading ? <Spinner /> : null}
                {
                    Object.keys(selected_package).length > 0 ? (
                        <div className="container-fluid">
                            <br></br>
                            <div className="payment-checkout-wrapper">
                                <h2 className="pull-lef payment-heading-des">{t('Confirm Your Order')}</h2>
                                <div className="payment-checkout-btn">
                                    <button className="btn btn-primary pull-right" onClick={() => window.location.href = "/packages/upgrade-package"}>
                                        <span>{t('Back')}</span>
                                    </button>
                                </div>
                            </div>

                            <div className="checkout-des">
                                {
                                    Object.keys(profile).length > 0 ? (
                                        <form onSubmit={handleVoucherSubmit}>
                                            <div className="form-row">
                                                <div className="form-group col-md-12 pl-0">
                                                    <label htmlFor='voucher'>{t('HaveVoucher')}</label>
                                                    <input
                                                        type='text'
                                                        className={`form-control ${hasErrorFor('voucher') ? 'is-invalid' : ''}`}
                                                        name='voucher'
                                                        value={voucher}
                                                        onChange={handleFieldChange}
                                                        placeholder={t('Voucher Code')}
                                                    />
                                                    {renderErrorFor('voucher')}
                                                </div>
                                            </div>

                                            <div className="form-action-btn btn-line d-flex justify-content-center ">
                                                <button className='btn btn-primary' disabled={disabledVoucherApplyBtn}>
                                                    <span>
                                                        {t('Apply')}
                                                    </span>
                                                </button>
                                            </div>
                                        </form>
                                    ) : ""
                                }
                                <div className='inner-des'>
                                    <div className='d-flex text-w'>
                                        {t('Package')}
                                    </div>
                                    <div className='d-flex'>
                                        {selected_package.title}
                                    </div>
                                </div>
                                <div className='inner-des'>
                                    <div className='d-flex text-w'>
                                        {t('price')}
                                    </div>
                                    <div className='d-flex'>
                                        <sup>???</sup>{price.toFixed(2)}
                                    </div>
                                </div>
                                <div className='inner-des'>
                                    <div className='d-flex text-w'>
                                        {t('VAT')} ({vat_percentage}%)
                                    </div>
                                    <div className='d-flex'>
                                        <sup>???</sup>{vat_amount}
                                    </div>
                                </div>
                                <div className='inner-des inner-des-wrap'>
                                    <div className='d-flex text-w'>
                                        {t('Discount')} ({discount_percentage}%)
                                    </div>
                                    <div className='d-flex'>
                                        <sup>???</sup>{discount_amount}
                                    </div>
                                </div>
                                <div className='inner-des'>
                                    <div className='d-flex text-sz'>
                                        <strong>{t('Total')}</strong>
                                    </div>
                                    <div className='d-flex'>
                                        <sup>???</sup>{((Number(price) + Number(vat_amount)) - Number(discount_amount)).toFixed(2)}
                                    </div>
                                </div>
                            </div>
                            <div className='payment-checkout-wrapper'>
                                <h2>{t('Payment Methods')}</h2>
                            </div>
                            <div className="add-cloud-icon payment-methods-icon">
                                {
                                    payment_gateway_settings && Object.keys(payment_gateway_settings).length > 0 ? (
                                        // payment_gateway_settings != {} ? (
                                        <>
                                            <ul>
                                                {
                                                    payment_gateway_settings.paypal_status ? (
                                                        <li>
                                                            <Link to="#" onClick={(event) => handlePaymentMethod(event, Constants.PAYMENT_METHODS.PAYPAL)}>
                                                                <div className="app-icon">
                                                                    <img src="/images/paypal.png" className="img-responsive center-block" alt="" />
                                                                </div>
                                                            </Link>
                                                        </li>
                                                    ) : null
                                                }
                                                {/* {
                                                        payment_gateway_settings.mollie_status ? (
                                                            <li>
                                                                <Link to="#" onClick={(event) => handlePaymentMethod(event, Constants.PAYMENT_METHODS.MOLLIE)}>
                                                                    <div className="app-icon">
                                                                        <img src="/images/mollie.png" className="img-responsive center-block" alt="" />
                                                                    </div>
                                                                </Link>
                                                            </li>
                                                        ) : null
                                                    } */}
                                            </ul>
                                            {
                                                payment_gateway_settings.mollie_status ?
                                                    <div className="checkout-pg panel panel-primary">
                                                        <div className="panel-body">
                                                            <Link to="#" className="text-on-pannel" onClick={(event) => handlePaymentMethod(event, Constants.PAYMENT_METHODS.MOLLIE)}>
                                                                <strong className="text-uppercase"> Mollie {t('checkout')} </strong>
                                                            </Link>
                                                            <div className="card-images-holder mb-3">
                                                                <ul className="list-unstyled">
                                                                    <li>
                                                                        <svg width="30" height="30" ><defs><path d="M22.5 0h-15A7.5 7.5 0 0 0 0 7.5v15A7.5 7.5 0 0 0 7.5 30h15a7.5 7.5 0 0 0 7.5-7.5v-15A7.5 7.5 0 0 0 22.5 0z" id="creditcard-a"></path><linearGradient x1="0%" y1="100%" y2="0%" id="creditcard-c"><stop stopColor="#060B0E" offset="0%"></stop><stop stopColor="#254764" offset="100%"></stop></linearGradient><linearGradient x1="27.635%" y1="25.476%" x2="22.589%" y2="11.652%" id="creditcard-d"><stop stopColor="#FFF" stopOpacity="0" offset="0%"></stop><stop stopColor="#FFF" offset="100%"></stop></linearGradient><linearGradient x1="50.002%" y1="100%" x2="38.944%" y2="55.526%" id="creditcard-e"><stop stopColor="#FFF" stopOpacity="0" offset="0%"></stop><stop stopColor="#FFF" offset="100%"></stop></linearGradient><linearGradient x1="38.944%" y1="55.526%" x2="50.001%" y2="100%" id="creditcard-f"><stop stopColor="#FFF" stopOpacity="0" offset="0%"></stop><stop stopColor="#FFF" offset="100%"></stop></linearGradient><linearGradient x1="49.992%" y1="0%" x2="49.992%" y2="100%" id="creditcard-g"><stop stopColor="#FAD961" offset="0%"></stop><stop stopColor="#F7955D" offset="100%"></stop></linearGradient></defs><g fill="none" fillRule="evenodd"><mask id="creditcard-b" fill="#fff"><use xlinkHref="#creditcard-a"></use></mask><g mask="url(#creditcard-b)" fillRule="nonzero"><path d="M32.5 27h-15a7.5 7.5 0 0 0-7.5 7.5v15a7.5 7.5 0 0 0 7.5 7.5h15a7.5 7.5 0 0 0 7.5-7.5v-15a7.5 7.5 0 0 0-7.5-7.5z" fill="url(#creditcard-c)" transform="translate(-10 -27)"></path><path d="M45.156 94.5c16.31 0 29.532-13.222 29.532-29.531 0-16.31-13.222-29.532-29.532-29.532-16.31 0-29.531 13.222-29.531 29.532 0 16.31 13.222 29.531 29.531 29.531z" fill="url(#creditcard-d)" opacity=".1" transform="translate(-10 -27)"></path><path d="M16.094 72c8.543 0 15.468-6.926 15.468-15.469s-6.925-15.468-15.467-15.468c-8.542 0-15.47 6.925-15.47 15.467C.625 65.072 7.551 72 16.094 72z" fill="url(#creditcard-e)" opacity=".1" transform="translate(-10 -27)"></path><path d="M49.844 50.438c13.72 0 24.843-11.123 24.843-24.844C74.687 11.873 63.565.75 49.844.75S25 11.873 25 25.594c0 13.72 11.123 24.843 24.844 24.843v.001z" fill="url(#creditcard-f)" opacity=".1" transform="translate(-10 -27)"></path><path d="M9.375 13.125H4.687a.937.937 0 0 0-.937.938v2.812c0 .518.42.938.938.938h4.687c.518 0 .938-.42.938-.938v-2.813a.937.937 0 0 0-.938-.937z" fill="#000"></path><path d="M19.375 40.125h-4.688a.937.937 0 0 0-.937.938v2.812c0 .518.42.938.938.938h4.687c.518 0 .938-.42.938-.938v-2.813a.937.937 0 0 0-.938-.937z" fill="url(#creditcard-g)" transform="translate(-10 -27)"></path><path d="M5.625 14.063h-.938v2.812h.938v-2.813.001zm1.875 0h-.938v2.812H7.5v-2.813.001zm1.875 0h-.938v2.812h.938v-2.813.001z" fill="#7A3802" opacity=".3"></path><path d="M8.438 20.625H3.75v.938h4.688v-.938zM6.563 22.5H3.75v.938h2.813V22.5zm3.75 0H7.5v.938h2.813V22.5zm3.75 0H11.25v.938h2.813V22.5zm0-1.875H9.375v.938h4.688v-.938zm5.625 0H15v.938h4.688v-.938zm5.625 0h-4.688v.938h4.688v-.938z" fillOpacity=".8" fill="#FFF"></path></g></g></svg>
                                                                        <p>Credit Card</p>
                                                                    </li>
                                                                    <li>
                                                                        <svg width="30" height="30" fill="none"><path d="M22 0H8a8 8 0 0 0-8 8v14a8 8 0 0 0 8 8h14a8 8 0 0 0 8-8V8a8 8 0 0 0-8-8z" fill="#FD6EB6"></path><path d="M8.438 9.375v11.25h7.538c1.646-.024 2.951-.427 3.877-1.202 1.126-.942 1.696-2.43 1.696-4.423 0-.952-.15-1.806-.446-2.539a4.626 4.626 0 0 0-1.244-1.77c-.955-.836-2.296-1.287-3.883-1.313l-7.539-.003h.001z" fill="#fff"></path><path d="M8.438 9.024l5.653.002h1.89c1.668.027 3.088.507 4.11 1.4a4.979 4.979 0 0 1 1.338 1.903c.314.777.472 1.676.472 2.671 0 2.09-.61 3.677-1.823 4.693-.992.83-2.373 1.258-4.102 1.284h-7.89V9.024h.352z" stroke="#fff" strokeWidth=".938"></path><path fillRule="evenodd" clipRule="evenodd" d="M9.895 19.14h2.101v-3.432H9.895v3.433-.001zm2.26-5.149a1.18 1.18 0 0 1-1.19 1.17 1.18 1.18 0 0 1-1.189-1.17c0-.646.533-1.17 1.19-1.17a1.18 1.18 0 0 1 1.189 1.17z" fill="#0A0B09"></path><path fillRule="evenodd" clipRule="evenodd" d="M15.833 10.123c1.461 0 2.679.39 3.522 1.126.952.833 1.435 2.095 1.435 3.751 0 3.282-1.621 4.878-4.957 4.878H9.197v-9.755h6.636zm.143-.748H8.437v11.25h7.539v-.002c1.646-.022 2.951-.425 3.877-1.2 1.126-.942 1.696-2.43 1.696-4.423 0-.952-.15-1.806-.446-2.539a4.626 4.626 0 0 0-1.244-1.77c-.955-.836-2.296-1.287-3.883-1.313v-.003z" fill="#0A0B09"></path><path fillRule="evenodd" clipRule="evenodd" d="M15.833 10.123c1.461 0 2.679.39 3.522 1.126.952.833 1.435 2.095 1.435 3.751 0 3.282-1.621 4.878-4.957 4.878H9.197v-9.755h6.636zm.143-.748H8.437v11.25h7.539v-.002c1.646-.022 2.951-.425 3.877-1.2 1.126-.942 1.696-2.43 1.696-4.423 0-.952-.15-1.806-.446-2.539a4.626 4.626 0 0 0-1.244-1.77c-.955-.836-2.296-1.287-3.883-1.313v-.003z" fill="#0A0B09"></path><path fillRule="evenodd" clipRule="evenodd" d="M15.626 19.144H12.96v-8.112h2.665-.108c2.223 0 4.589.863 4.589 4.067 0 3.387-2.366 4.045-4.589 4.045h.109z" fill="#CD0067"></path><path fillRule="evenodd" clipRule="evenodd" d="M12.901 13.457v1.29h.3c.112 0 .193-.006.243-.018a.373.373 0 0 0 .162-.081.41.41 0 0 0 .105-.188c.027-.088.04-.206.04-.357 0-.15-.013-.266-.04-.347a.443.443 0 0 0-.114-.188.385.385 0 0 0-.186-.092 1.859 1.859 0 0 0-.33-.019h-.18zm-.4-.33h.732c.165 0 .291.012.378.037a.665.665 0 0 1 .298.18c.086.09.15.198.19.316.043.124.065.278.065.46 0 .162-.02.3-.061.416a.86.86 0 0 1-.212.345.708.708 0 0 1-.282.156c-.115.03-.235.045-.354.041H12.5v-1.952l.001.001zm2.115 1.951v-1.952h1.469v.331h-1.069v.432h.994v.331h-.994v.527h1.107v.331h-1.507zm3.009-.774l-.273-.723-.268.723h.541zm.738.774h-.436l-.173-.443h-.793l-.164.443h-.425l.771-1.952h.426l.794 1.952zm.334 0v-1.936h.4v1.605h.997v.331h-1.397z" fill="#fff"></path></svg>
                                                                        <p>Ideal</p>
                                                                    </li>
                                                                    <li>
                                                                        <svg width="30" height="30" fill="none" ><path d="M22.5 0h-15A7.5 7.5 0 0 0 0 7.5v15A7.5 7.5 0 0 0 7.5 30h15a7.5 7.5 0 0 0 7.5-7.5v-15A7.5 7.5 0 0 0 22.5 0z" fill="#FF9500"></path><path fillRule="evenodd" clipRule="evenodd" d="M17.983 8c-1.265 0-2.492 0-3.593.559-1.1.559-2.071 1.678-2.819 3.915-.162.482-.248.896-.259 1.256a1.9 1.9 0 0 0 .188.934c.168.325.441.631.782.889.342.259.75.468 1.19.602l.529.151c.28.08.606.177.813.24.12.037.254.085.374.158a.75.75 0 0 1 .298.301.667.667 0 0 1 .071.29.942.942 0 0 1-.055.335c-.103.304-.259.5-.555.62-.296.119-.731.157-1.392.162h-8.25L4 22h8.831c.831 0 2.35 0 3.81-.622 1.46-.62 2.861-1.861 3.454-4.346.223-.933.154-1.693-.211-2.291-.367-.597-1.03-1.03-1.994-1.307l-.47-.136-.695-.2a.933.933 0 0 1-.55-.396.78.78 0 0 1-.081-.628.92.92 0 0 1 .458-.551c.232-.129.537-.2.893-.2h5.134c.036-.052.069-.109.105-.16C23.622 9.832 24.757 8.758 26 8h-8.017z" fill="#fff"></path></svg>
                                                                        <p>SOFORT Banking</p>
                                                                    </li>
                                                                    <li>
                                                                        <svg width="30" height="30" fill="none" ><path d="M22 0H8a8 8 0 0 0-8 8v14a8 8 0 0 0 8 8h14a8 8 0 0 0 8-8V8a8 8 0 0 0-8-8z" fill="#fff"></path><path d="M8 .5h14A7.5 7.5 0 0 1 29.5 8v14a7.5 7.5 0 0 1-7.5 7.5H8A7.5 7.5 0 0 1 .5 22V8A7.5 7.5 0 0 1 8 .5z" stroke="#000" strokeOpacity=".1"></path><path fillRule="evenodd" clipRule="evenodd" d="M3 16.651a1.14 1.14 0 0 1 1.133-1.146h3.452l-1.72 1.73h6.878l3.439-4.038h7.45l-4.97 5.504c-.42.465-1.274.842-1.893.842H4.132A1.138 1.138 0 0 1 3 18.397V16.65z" fill="#00549D"></path><path fillRule="evenodd" clipRule="evenodd" d="M27 13.859c0 .63-.513 1.14-1.132 1.14h-3.879l1.572-1.73h-7.45l-3.373 4.038-6.892-.057 4.954-5.99c.4-.483 1.24-.876 1.864-.876h13.198A1.14 1.14 0 0 1 27 11.525v2.334z" fill="#FFD800"></path></svg>
                                                                        <p>Bancontact</p>
                                                                    </li>
                                                                    <li>
                                                                        <svg width="30" height="30" fill="none" ><path d="M22 0H8a8 8 0 0 0-8 8v14a8 8 0 0 0 8 8h14a8 8 0 0 0 8-8V8a8 8 0 0 0-8-8z" fill="#DE378C"></path><path fillRule="evenodd" clipRule="evenodd" d="M12.771 16.611c.387-.792 1.164-1.407 2.057-1.407.894 0 1.672.615 2.058 1.407H12.77zM14.828 5C16.91 5 18.6 6.748 18.6 8.905v1.094c0 .14-.117.279-.252.279H16.96c-.136 0-.246-.139-.246-.28V8.906c0-1.077-.845-1.953-1.885-1.953s-1.886.876-1.886 1.953V10c0 .14-.119.279-.254.279H11.3c-.136 0-.244-.139-.244-.28V8.818a.14.14 0 0 1 0-.019C11.113 6.691 12.78 5 14.828 5zm-4.23 13.924c.485 2.038 2.279 3.598 4.412 3.598h5.963A1.767 1.767 0 0 1 19.24 24h-8.478C9.792 24 9 23.162 9 22.169v-8.702c0-.993.792-1.782 1.76-1.782h8.48c.968 0 1.76.813 1.76 1.807l-.008 6.666H15.01c-.917 0-1.711-.444-2.104-1.232h6.654v-1.13c0-2.57-2.055-4.66-4.56-4.66-2.13 0-3.92 1.514-4.41 3.547 0 0-.132.68-.132 1.124 0 .444.14 1.117.14 1.117z" fill="#fff"></path></svg>
                                                                        <p>eps</p>
                                                                    </li>
                                                                    <li>
                                                                        <svg width="30" height="30" fill="none" ><path d="M22 0H8a8 8 0 0 0-8 8v14a8 8 0 0 0 8 8h14a8 8 0 0 0 8-8V8a8 8 0 0 0-8-8z" fill="#0F4CA1"></path><path fillRule="evenodd" clipRule="evenodd" d="M16.25 21.188h2.884v-4.642h.032c.546 1.017 1.636 1.394 2.645 1.394 2.484 0 3.814-2.1 3.814-4.626C25.625 11.247 24.36 9 22.052 9c-1.314 0-2.533.541-3.11 1.739h-.032V9.197h-2.66v11.99zm6.395-7.808c0 1.361-.657 2.296-1.747 2.296-.962 0-1.764-.934-1.764-2.182 0-1.279.706-2.23 1.764-2.23 1.122 0 1.747.983 1.747 2.116zm-8.27-4.186h-2.649v1.516h-.032C11.077 9.678 10.07 9 8.818 9 6.186 9 5 10.872 5 13.374c0 2.487 1.446 4.23 3.77 4.23 1.17 0 2.144-.452 2.842-1.438h.033v.453c0 1.647-.91 2.438-2.6 2.438-1.218 0-1.965-.258-2.859-.694l-.146 2.292a9.944 9.944 0 0 0 3.233.532c3.412 0 5.102-1.114 5.102-4.568V9.194zm-6.353 4.132c0-1.146.569-2.098 1.657-2.098 1.316 0 1.869 1.048 1.869 1.985 0 1.292-.83 2.163-1.869 2.163-.877 0-1.657-.743-1.657-2.05z" fill="#FFFFFE"></path></svg>
                                                                        <p>Giropay</p>
                                                                    </li>
                                                                    <li>
                                                                        <svg width="30" height="30" fill="none" ><rect width="30" height="30" rx="8" fill="#014C6F"></rect><path fillRule="evenodd" clipRule="evenodd" d="M15.096 13.814c1.904 0 3.447-1.526 3.447-3.407C18.543 8.525 17 7 15.096 7c-1.903 0-3.446 1.525-3.446 3.407 0 1.881 1.543 3.407 3.446 3.407z" fill="#fff"></path><path fillRule="evenodd" clipRule="evenodd" d="M14.338 14.57a4.608 4.608 0 0 1-2.777-.934c-3.763.39-6.561.943-6.561.943v1.449h20v-3.05s-3.458-.021-7.362.185a4.552 4.552 0 0 1-3.3 1.408zm-6.967 7.821H5v-5.454h2.371v2.454l1.41-2.454h2.843l-2.082 2.819 1.965 2.635H8.793l-1.405-2.05-.017 2.05zm17.591-.289c-.56.182-1.295.29-1.953.29-2.346 0-4.068-.711-4.068-2.777 0-1.867 1.67-2.678 3.951-2.678.634 0 1.44.074 2.07.289v1.808c-.468-.301-.935-.49-1.604-.49-.87 0-1.74.457-1.74 1.12 0 .665.866 1.12 1.74 1.12.659 0 1.136-.194 1.604-.482v1.8zm-10.578-2.854h1.18c.457 0 .636-.204.636-.517 0-.359-.18-.538-.602-.538h-1.214v1.055zm0 2.034h1.236c.464 0 .621-.212.621-.525 0-.313-.159-.538-.615-.538h-1.242v1.063zm-2.372-4.345h4.794c1.245 0 1.653.535 1.653 1.367 0 .932-.754 1.337-1.528 1.383v.015c.795.069 1.57.168 1.57 1.268 0 .719-.408 1.421-1.778 1.421h-4.71v-5.454z" fill="#fff"></path></svg>
                                                                        <p>KBC Payment</p>
                                                                    </li>
                                                                    <li>
                                                                        <svg width="30" height="30" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="30" height="30" rx="8" fill="#014C6F"></rect><path fillRule="evenodd" clipRule="evenodd" d="M12.003 16.869h4.69c1.227 0 1.63.537 1.63 1.372 0 .92-.742 1.325-1.504 1.385.784.071 1.525.179 1.525 1.265 0 .728-.381 1.432-1.715 1.432h-4.626V16.87zm2.318 4.344h1.228c.455 0 .593-.215.593-.537 0-.298-.138-.513-.593-.513h-1.228v1.05zm0-2.053h1.175c.434 0 .603-.19.603-.513 0-.346-.169-.537-.571-.537H14.32v1.05zm-3.419 2.874c-.55.185-1.27.29-1.916.29-2.297 0-3.97-.706-3.97-2.763 0-1.884 1.63-2.692 3.853-2.692.625 0 1.408.069 2.033.277v1.837c-.455-.312-.91-.497-1.577-.497-.837 0-1.705.474-1.705 1.121 0 .66.868 1.121 1.705 1.121.645 0 1.122-.208 1.577-.497v1.803zm13.984 0c-.55.185-1.27.29-1.916.29-2.297 0-3.98-.706-3.98-2.763 0-1.884 1.63-2.692 3.863-2.692.614 0 1.408.069 2.033.277v1.837c-.477-.312-.932-.497-1.577-.497-.847 0-1.705.474-1.705 1.121 0 .66.837 1.121 1.705 1.121.645 0 1.1-.208 1.577-.497v1.803z" fill="#FFFFFE"></path><path fillRule="evenodd" clipRule="evenodd" d="M15.232 13.739c1.852 0 3.355-1.516 3.355-3.38A3.365 3.365 0 0 0 15.232 7a3.367 3.367 0 0 0-3.367 3.359 3.372 3.372 0 0 0 3.367 3.38z" fill="#fff"></path><path fillRule="evenodd" clipRule="evenodd" d="M14.49 14.48a4.556 4.556 0 0 1-2.72-.911C8.107 13.95 5 14.5 5 14.5v1.459h20v-3.037s-3.47-.022-7.27.17a4.516 4.516 0 0 1-3.24 1.387z" fill="#fff"></path></svg>
                                                                        <p>CBC Payment</p>
                                                                    </li>
                                                                    <li>
                                                                        <svg width="30" height="30" fill="none" ><rect width="30" height="30" rx="8" fill="#D50043"></rect><path fillRule="evenodd" clipRule="evenodd" d="M8.438 10.65a2.21 2.21 0 0 1 2.211-2.213h8.702a2.21 2.21 0 0 1 2.212 2.212v10.914H10.649a2.21 2.21 0 0 1-2.211-2.212v-8.702zm2.812 2.365v.22c0 .448.372.828.831.828h5.838a.83.83 0 0 0 .831-.828v-.22a.835.835 0 0 0-.831-.828H12.08a.83.83 0 0 0-.831.828zm0 3.75v.22c0 .448.372.828.831.828h5.838a.83.83 0 0 0 .831-.828v-.22a.835.835 0 0 0-.831-.828H12.08a.83.83 0 0 0-.831.828z" fill="#fff"></path></svg>
                                                                        <p>Belfius Pay</p>
                                                                    </li>
                                                                    <li>
                                                                        <svg width="30" height="30" fill="none" ><path d="M22 0H8a8 8 0 0 0-8 8v14a8 8 0 0 0 8 8h14a8 8 0 0 0 8-8V8a8 8 0 0 0-8-8z" fill="#F36717"></path><path d="M14.806 11.684c.984 0 .958.434.958 1.454v2.525c0 .179 0 .255.026.383l-4.583-4.643h-2.46v.28c1.036 0 1.036.639 1.036 1.225v3.929c0 .995 0 1.454-.984 1.454v.28h2.615v-.28c-.984 0-.984-.383-.984-1.454v-3.444c0-.153 0-.28-.026-.434l5.567 5.663h.44v-5.484c0-1.046.052-1.454 1.01-1.454v-.28h-2.615v.28zm-11.056 0c.725 0 1.062.025 1.062.944v4.719c0 .893-.44.944-1.062.944v.28h3.677v-.28c-.674 0-1.062-.051-1.062-.944v-4.72c0-.918.31-.943 1.062-.943v-.28H3.75v.28zm19.212 3.163v.28c.88 0 .984.23.984.842v1.888c-.518.23-.933.332-1.399.332-1.553 0-2.718-1.301-2.718-3.189 0-1.913.958-3.189 2.589-3.189.777 0 1.45.255 1.942.74.31.28.543.689.673 1.173h.31l-.232-2.474h-.26c-.025.179-.077.485-.258.485-.207 0-.414-.128-.751-.23a4.504 4.504 0 0 0-1.502-.255c-2.382 0-4.246 1.786-4.246 3.929 0 1.99 1.631 3.571 4.35 3.571.984 0 1.968-.255 3.003-.765v-1.837c0-.765-.025-1.02.803-1.02v-.281h-3.288z" fill="#fff"></path></svg>
                                                                        <p>ING Home'Pay</p>
                                                                    </li>
                                                                    <li>
                                                                        <svg width="30" height="30" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="30" height="30" rx="8" fill="#ABD3C3"></rect><path d="M9 8h12v11l-6 6-6-6V8z" fill="#009586"></path><path fillRule="evenodd" clipRule="evenodd" d="M15 13l6 6-6 6V13z" fill="#FCBF00"></path></svg>
                                                                        <p>ABN AMRO</p>
                                                                    </li>
                                                                    <li>
                                                                        <svg width="30" height="30" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M22 0H8a8 8 0 0 0-8 8v14a8 8 0 0 0 8 8h14a8 8 0 0 0 8-8V8a8 8 0 0 0-8-8z" fill="#FF5300"></path><path fillRule="evenodd" clipRule="evenodd" d="M30 12.93l-.02-.024c-.15-.181-.3-.362-.48-.544-.235 0-.42.157-.586.299-.088.074-.17.145-.253.186-.045.092-.142.184-.232.27-.146.139-.274.26-.128.334.086.172.23.222.391.28.068.023.139.048.21.084.172.174.346.285.564.425.085.054.176.113.276.18.093.096.178.196.258.298v-1.787zm0 3.25a22.722 22.722 0 0 1-.103-.026c-.149-.038-.323-.084-.517-.13h-.62c.076.217.246.432.394.62.093.12.178.227.226.317 0 .119 0 .471.127.703a1.8 1.8 0 0 1 .493.091v-1.574zm0 4.8c-.206-.033-.366-.072-.366-.072 0 .18.032.417.064.654.03.237.062.473.062.65 0 .055.107.316.234.592.004-.101.006-.202.006-.304v-1.52zm-1.592 6.14a4.09 4.09 0 0 1-.31-.281.79.79 0 0 0-.323.074.44.44 0 0 1-.162.045 3.03 3.03 0 0 0-.286.173 2.751 2.751 0 0 1-.314.187c.194.258.388.48.582.686a7.55 7.55 0 0 0 .813-.883zm-2.091 1.837c-.263-.383-.497-.838-.497-1.005-.1-.05-.158-.08-.22-.096-.082-.022-.17-.022-.378-.022-.123-.123-.364-.123-.482-.123 0 0 .118.601.36 1.2.101.2.282.398.412.46.277-.122.546-.26.805-.414zm-2.07.838c-.048-.105-.033-.262-.017-.422.03-.295.06-.6-.316-.6-.247-.125-.998-.365-.998-.365-.13.364-.13 1.087 0 1.45 0 .036.006.08.018.13.45-.026.888-.091 1.313-.193zM21.744 30v-1.068a3.243 3.243 0 0 0-.557.183.534.534 0 0 1-.196.058l-.139.089c-.294.185-.481.302-.481.511 0 .076.003.152.008.227h1.365zm-2.11 0a11.792 11.792 0 0 0-.136-.703l-.004-.017c-.093.022-.203.043-.321.066-.523.101-1.193.23-1.193.516a.893.893 0 0 0-.038.138h1.692zm-2.854 0a.727.727 0 0 0-.469-.167c-.094 0-.24-.051-.386-.102-.243-.085-.485-.169-.485-.02-.28 0-.333.122-.307.289h1.648zm-2.713 0a2.05 2.05 0 0 0 .186-.902s-.12-.607-.24-.85c-.123-.243-.246-.485-.608-.728a.448.448 0 0 1-.175-.071c-.11-.062-.232-.13-.307-.054 0 0-.123.973-.243 1.096.07.105.14.2.208.29.165.224.31.422.395.68l.074.234c.031.098.064.201.099.305h.611zm-4.985 0a2.493 2.493 0 0 1-.294-.527 3.574 3.574 0 0 0-.09-.192c-.404 0-1.73.49-2.168.657.317.04.641.062.97.062h1.582zm-2.109-1.02c0 .24-.377.475-.377.475 0-.278-.044-.597-.081-.861a3.772 3.772 0 0 1-.049-.443 2.658 2.658 0 0 0-.105-.181c-.082-.134-.148-.242-.148-.41.126-.118.366-.232.605-.346l.027-.013c.087-.162.405-.377.64-.536.11-.075.202-.137.242-.175.378.355.503.473.758 1.07-.02.019-.059.05-.11.093-.262.22-.876.733-1.402 1.327zm-1.46-5.44v-.586c.252-.119.535-.267.816-.415.28-.147.56-.293.807-.41-.028.136-.057.253-.084.364-.09.37-.166.678-.166 1.401 0 .108.052.314.121.584.082.322.187.734.255 1.181l-.14.272c-.17.34-.312.622-.611.903a.875.875 0 0 1-.372.354c0-.237-.123-.472-.253-.706-.06-.176-.09-.35-.121-.526-.031-.177-.062-.355-.124-.533 0-.194-.038-.672-.073-1.102a17.133 17.133 0 0 1-.054-.78zm4.985 3.02c.213.098.458.212.55.402.133.204.114.447.095.684-.014.177-.027.35.022.501.116.593.466 1.657.466 1.657a4.687 4.687 0 0 0-.531-.3 4.768 4.768 0 0 1-.516-.29l-.008-.004c-.23-.119-.458-.237-.458-.354-.116-.354-.235-1.184-.235-1.42 0-.474.235-.946.469-.946.045.023.095.046.146.07zm-4.61-4.84c-.269.294-.524.573-.651.758l-.125-1.454c-.123-.365-.123-.365-.123-1.088.309-.182.525-.333.742-.484.219-.153.437-.306.75-.489.23-.226 1.019-.456 2.152-.786l.202-.06c0 .398-.085.714-.184 1.084-.02.08-.043.162-.065.247-1.074.527-1.59 1.052-1.873 1.34-.043.043-.08.081-.114.113-.18.24-.452.536-.711.82zm1.708-4.42c-.358.263-.715.524-.774.582-.23.112-.346.223-.668.532l-.055.053a3.381 3.381 0 0 1-.117-.521c-.03-.177-.059-.353-.119-.529 0-.12 0-.935.119-1.287 0-.235 1.079-.583 1.676-.698.066 0 .184-.017.324-.038.375-.056.904-.134.994.038.056.108.033.165-.007.267-.047.116-.117.291-.117.666-.062.125-.125.15-.207.181-.071.028-.157.06-.268.169-.059.058-.42.322-.78.586zm13.925 8.783c0 .118-.243.58-.49.813-.365-.464-1.102-.93-1.102-.93.091.34.05.494-.029.78-.028.105-.062.227-.096.384 0 .116-.858.116-.858.116-.732-.35-.855-.814-.855-.814 0 .116-.126.93-.616.93-.488 0-.856 0-.856-.346-.127-.352-.127-.584-.127-.933v-.464c-.276.088-.485.31-.678.513-.061.065-.12.128-.18.185-.06 0-.12-.058-.18-.117-.062-.058-.123-.117-.184-.117a5.469 5.469 0 0 1-.492-1.278c1.226-.817 2.453-.931 3.557-.931 1.226 0 2.454.114 3.434.697 0 .466 0 1.048-.248 1.512zm-14.918-11c-.247.24-.247.24-.496.593 0 0-.121-1.303-.244-2.013.362-.174.59-.347.884-.568.107-.08.222-.166.354-.262-.028-.16-.056-.309-.082-.45-.091-.48-.165-.867-.165-1.322 0-.358 0-.712.247-1.068.25-.474.867-.474 1.361-.356-.037.259-.076.46-.11.643a6.156 6.156 0 0 0-.137 1.369c0 .12.247.712.372.95.123.234.248.473.248.709 0 .118-.125.238-.248.355a1.33 1.33 0 0 1-.368.165c-.113.035-.182.056-.25.19-.26.198-.455.356-.63.498a13.84 13.84 0 0 1-.736.568zm8.497 6.349c-.365 0-.847.11-.847.11s.12.443.24.664a2.41 2.41 0 0 0 .522-.225c.064-.036.128-.07.204-.106 0 0 .484-.222.607-.333-.241-.11-.482-.11-.726-.11zm.159-3.136c-.36.118-.845.354-1.084.707-.238.356-.36.95 0 1.184.143.07.286-.027.455-.143a1.59 1.59 0 0 1 .386-.212c.297-.097.35-.274.427-.53.016-.056.034-.116.056-.179.105-.104.258-.185.406-.264.188-.1.367-.195.435-.327v-.354a73.4 73.4 0 0 0 .082-.786 14.1 14.1 0 0 1 .16-1.225c0-.06.029-.266.059-.473.03-.207.06-.414.06-.473 0-.234-.244-.47-.601-.592h-1.563c-.6 0-1.085 0-1.682.122.193.378.617.758.903 1.015.07.062.132.117.18.163.237.238.476.475.961.475.24-.06.36-.06.449-.06.09 0 .149 0 .268-.06 0 .076.012.163.025.254.028.197.059.413-.025.576l.003.068c.017.25.066 1.009-.36 1.114zm-3.256-5.577c.072.061.137.117.191.166.192 0 .318-.153.48-.35.151-.185.335-.409.635-.578.248-.235.995-.7.995-.7.124 0 .124-.112.124-.232-.214-.706-.43-.8-.808-.963a7.037 7.037 0 0 1-.186-.083c-.371-.233-.62-.233-.866-.233-.5.116-1.37.35-1.617 1.047a.812.812 0 0 1-.006.073c-.019.181-.06.577.254.974.194.365.542.659.804.88zm3.732.096l-.008.006c-.079.08-.152.15-.218.211-.235.222-.382.36-.382.647h.6c.207 0 .356.029.473.052.22.043.325.063.483-.176.096-.13.191-.244.28-.349.244-.289.436-.516.436-.876 0-.247-.476-.123-.716 0-.24 0-.592.24-.948.485zm5.949.451c1.302-.567-.713-1.023-.947-1.023-.11-.02-.24-.05-.376-.08-.6-.132-1.318-.291-.928.08 0 .132.119.187.266.256.106.048.226.104.327.2.234 0 .234.455.234.68.156 0 .31.008.46.016.419.021.787.04.964-.129zm2.856-3.65c-.496-.119-.999 0-1.375.357l-.072.103c-.238.343-.539.774-.425 1.204.873.24 1.497.717 2.123 1.43.373 0 .747-.238.871-.596.128-.594.252-1.545-.123-2.022l-.002-.001c-.25-.238-.374-.357-.997-.475zm-1.168 5.847c.24-.12.478-.24.598-.471.126 0 .246-.24.246-.24-1.108-.123-3.454-.123-3.454-.123-.372.124-.74.48-.74.6 0 0 .246 1.32.246 1.44 0 .42.03.63.061.84.03.21.061.42.061.839.14.09.245.145.334.193.148.08.254.136.406.287.108.203.213.409.317.614l.053.103c.096.047.196.093.298.14.427.199.886.411 1.188.7.122 0 .49-.599.49-.84.322 0-.037-.203-.462-.444a7.31 7.31 0 0 1-.647-.395c-.126 0-.25-.12-.25-.12a1.68 1.68 0 0 1-.212-.28c-.148-.227-.313-.477-.65-.56 0 0-.125 0-.125-.119 0-.358-.121-1.798-.121-1.798.247.48.739.48.988.48.492 0 .86-.12 1.111-.48 0-.12.119-.357.245-.357l.018-.01zm-1.377 6.618c.172.088.34.174.526.223 0 0 .122-.631.122-.758a3.004 3.004 0 0 1-.212-.084c-.302-.126-.652-.273-1.238-.17a.493.493 0 0 0-.088.106c-.035.066-.035.15-.035.4.372 0 .652.143.925.283zm-6.716 1.668c-.22.13-.44.259-.661.259-.175 0-.349-.248-.477-.43a1.417 1.417 0 0 0-.127-.166.72.72 0 0 1-.19-.027c-.108-.026-.216-.053-.54.027-1.212.479-2.91 1.314-4.126 2.031-.148-.142-.163-.326-.177-.502a.875.875 0 0 0-.07-.334c1.338-.836 2.795-1.553 4.25-2.15.123-.122 0-.479-.118-.717 0 0-.851-.12-2.429.358-.638.155-1.328.468-1.76.664-.227.102-.383.173-.425.173 0 0-.119-.36-.119-.837a18.595 18.595 0 0 1 2.304-.956c.57-.162 1.14-.216 1.708-.27.28-.026.56-.052.84-.091-.119 0-.119-.239-.119-.358-.366-.716-.971-1.314-1.578-1.912-.245-.477-.487-1.074-.487-1.315.144-.142.288-.075.457.004.116.054.245.114.393.114-.122-.358-.242-.835-.242-1.314-.12-.477-.12-.478-.12-1.074 0-.37.063-.646.109-.842.042-.184.069-.299.01-.355-.12-.119-.607-.36-.729-.36-.052-.024-.099-.044-.141-.062-.154-.066-.25-.107-.342-.295a21.57 21.57 0 0 0-.162-.261c-.303-.482-.568-.903-.568-1.413 0-.513.178-.938.383-1.427l.103-.247c.095-.378.495-.606.838-.802.09-.052.177-.102.253-.151.274-.182.546-.16.819-.136.09.007.182.015.273.015.783 0 1.468.407 2.044.75l.143.085c.485.358.73.837.85 1.315h1.09l.026.006c.6.119 1.2.238 1.918.592.849-.717 2.308-.836 3.032-.836 0-1.316.973-2.033 1.702-2.033l.146-.03c.525-.105.897-.18 1.675.15.609.358 1.457 1.553 1.457 1.673.179.355.16.775.139 1.26-.008.17-.016.347-.016.533-.123.478-.123.836-.123.957-.12.477-.728.834-1.093.955 0 .54-.101.93-.206 1.331-.057.222-.116.447-.158.702 0 .343-.113.683-.226 1.026l-.016.051c.147-.05.255-.118.356-.182.147-.094.278-.177.495-.177-.164 1.038-.38 1.276-.615 1.534-.116.128-.237.26-.357.499-.119.477-.364.955-.727 1.433l-.008.008a3.441 3.441 0 0 1-.6.47c-.164.079-.11.158-.056.238.028.04.055.08.055.12 1.217 0 2.666.36 4.614 1.315.075.074.012.193-.076.356-.055.103-.12.224-.166.363-1.273-.714-2.54-.961-3.454-1.14a10.91 10.91 0 0 1-.798-.174v.955c.246.059.43.149.612.24.182.089.363.178.604.237.239.12 2.061 1.074 2.671 1.435 0 .24 0 .719-.242.719-1.309-.83-2.257-1.233-3.232-1.646-.292-.124-.587-.25-.895-.387a2.062 2.062 0 0 0-.254.322c-.124.18-.255.37-.475.514-.074.07-.408-.157-.767-.402a6.445 6.445 0 0 0-.69-.434c-.28-.185-.782-.298-1.22-.397-.127-.029-.25-.056-.36-.084-.12 0-.12-.237-.12-.356-.124-.358-.242-.716-.124-.835.609-.24.85-.721.85-1.079 0-.19.103-.145.254-.078.134.059.305.134.476.078.244-.238.364-.478.244-.716 0-.239-.122-.479-.362-.479a5.133 5.133 0 0 0-3.765 0c-.366.12-.366.6-.244.957.118.477.608.238.97 0 .047 0 .04.054.026.143-.02.137-.052.355.095.574.152.299.398.458.562.564.1.065.17.11.17.155 0 .203 0 .291-.023.373-.017.061-.047.12-.1.222 0 .24 0 .477-.246.6-.582.096-1.09.268-1.641.455-.137.046-.276.094-.42.141a2.395 2.395 0 0 0-.434.218zm12.008-6.38c0-.116.126-.354.247-.472.056.08.111.154.165.225.186.25.355.474.451.84.241.71.61 2.249.61 2.249-.237-.172-.474-.233-.697-.29-.237-.06-.46-.117-.65-.301-.284-.272-.352-.61-.422-.966a5.96 5.96 0 0 0-.074-.336c-.184-.272-.015-.475.186-.714a3.25 3.25 0 0 0 .184-.235zM12.315 6.378c-.486.482-1.949 1.33-2.807 1.813-.12.24-.73 1.083-.853 1.206l-1.219-.484c.122-.24.243-.42.365-.601.122-.182.245-.363.367-.605.372-.553.886-1.036 1.326-1.449.137-.128.266-.249.381-.364a12 12 0 0 0 1.014-.415c.51-.23.865-.39 1.918-.549.607.724 2.315 1.811 2.315 1.811 0 .241.126-.12.613-1.57.184.12.337.182.49.242.152.06.304.12.486.24.12 0 .488.123.488.123.12 0 .856-.363.978-.484.242-.122 1.341-.362 1.341-.362.079.077-.094.453-.228.743-.075.163-.138.299-.138.342v.967c.12 0 .245 0 .489-.122.485-.19.664-.922.839-1.639.044-.182.088-.363.137-.534.364.12.609.12.975.12 0 .142-.021.304-.041.462-.05.384-.097.746.164.746.296 0 .673-.318 1.196-.76l.39-.325c1.224.483 2.442 1.207 3.537 1.93-.105.07-.21.16-.318.252-.267.229-.552.473-.898.473-.297 0-.515-.078-.78-.173a11.566 11.566 0 0 0-.198-.069c-.245-.24-1.342-.602-1.955-.602-.432 0-.78.337-1.168.713-.268.26-.556.539-.904.737a8.116 8.116 0 0 1-.602.153c-.359.084-.793.185-1.228.328l-.003.002c-.487.24-.73.36-.73.12-.125-.485-.125-.968-.125-1.45a3.443 3.443 0 0 0-.415-.097c-.274-.052-.564-.107-.803-.265-.247.844-.489 1.209-.733 1.568a7.496 7.496 0 0 0-.487-.213c-.325-.134-.651-.268-.976-.511l-.175-.103c-.676-.397-1.91-1.123-2.025-1.345zm15.787 5.047c-.121-.35-.244-.706-.244-1.059 0-.471-.36-1.414-.603-2-.242-.355-.242-1.062-.242-1.062.24.094.462.17.672.24.317.109.608.208.897.348v.473c.082.649.281 1.242.474 1.817.087.26.173.515.247.771 0 .143.04.285.078.426.059.211.117.422.046.634 0 0-.726-.235-1.325-.588z" fill="#fff"></path></svg>
                                                                        <p>ING</p>
                                                                    </li>
                                                                    <li>
                                                                        <svg width="30" height="30" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient x1=".006%" y1="50.056%" x2="100.002%" y2="50.056%" id="RABONL2U-a"><stop stopColor="#CC612C" offset="0%"></stop><stop stopColor="#743237" offset="100%"></stop></linearGradient><linearGradient x1="-17.936%" y1="61.858%" x2="98.996%" y2="38.784%" id="RABONL2U-b"><stop stopColor="#FFDD7F" offset="0%"></stop><stop stopColor="#F47920" offset="100%"></stop></linearGradient><linearGradient x1="43.104%" y1="33.959%" x2="60.62%" y2="41.479%" id="RABONL2U-c"><stop stopColor="#2A3795" offset="0%"></stop><stop stopColor="#222A7E" offset="100%"></stop></linearGradient></defs><g fill="none" fillRule="evenodd"><path d="M22 0H8a8 8 0 0 0-8 8v14a8 8 0 0 0 8 8h14a8 8 0 0 0 8-8V8a8 8 0 0 0-8-8z" fill="#FFF" fillRule="nonzero"></path><path d="M8 .5h14A7.5 7.5 0 0 1 29.5 8v14a7.5 7.5 0 0 1-7.5 7.5H8A7.5 7.5 0 0 1 .5 22V8A7.5 7.5 0 0 1 8 .5z" strokeOpacity=".1" stroke="#000"></path><path d="M21.682 17.994c.328.335.6.724.808 1.15.622 1.316.394 2.766-.845 4.032-1.42 1.45-3.836 2.342-6.64 2.342-2.805 0-5.222-.892-6.636-2.342-1.237-1.266-1.467-2.716-.845-4.032.206-.425.476-.814.8-1.15-.396.399-.708.756-.993 1.345-.606 1.263-.415 3.07.867 4.379 1.452 1.488 3.733 2.383 6.83 2.383 3.098 0 5.383-.922 6.777-2.383 1.368-1.432 1.42-3.296.86-4.42a4.508 4.508 0 0 0-.983-1.304z" fill="url(#RABONL2U-a)" fillRule="nonzero"></path><path d="M19.404 16.476c-1.314-.567-2.846-.864-4.4-.864-1.554 0-3.085.293-4.4.864-1.438.622-2.552 1.544-3.085 2.673-.622 1.315-.393 2.765.845 4.031 1.42 1.45 3.836 2.343 6.64 2.343 2.805 0 5.221-.893 6.64-2.343 1.24-1.266 1.468-2.716.846-4.031-.533-1.129-1.647-2.051-3.086-2.673z" fill="#F47920" fillRule="nonzero"></path><path d="M15.266 17.11c-.26-.552-.208-1.053-.154-1.493h-.103c-1.556 0-3.086.292-4.4.864-1.44.621-2.553 1.543-3.086 2.672-.622 1.315-.394 2.767.845 4.033.086.088.178.174.272.259 5.22-.322 7.95-3.507 6.626-6.334v-.001z" fill="url(#RABONL2U-b)" fillRule="nonzero"></path><path d="M11.64 17.204c.959.256 1.904.564 2.833.924.185-.58.383-1.16.61-1.728.224.566.422 1.144.608 1.728a26.67 26.67 0 0 1 2.812-.911c-.662.439-1.302.914-1.915 1.425 1.284.17 2.488.392 3.75.677a40.38 40.38 0 0 0-3.705.102 41.831 41.831 0 0 1 2.472 2.394 47.046 47.046 0 0 0-3.37-1.817 150.44 150.44 0 0 0-.653 3.086c-.207-1.03-.399-2.01-.627-3.037a82.065 82.065 0 0 0-3.421 1.777c.8-.824 1.633-1.617 2.495-2.376-1.258-.084-2.465-.102-3.753-.121a42.772 42.772 0 0 1 3.796-.677c-.62-.517-1.264-1-1.933-1.446h.001zm3.442 1.795V16.73c-.22.63-.414 1.269-.599 1.912.201.117.401.236.6.356l-.001.001zm-2.877-1.545c.778.36 1.54.757 2.281 1.188-.184.02-.369.04-.554.062a25.538 25.538 0 0 0-1.727-1.25zM15.082 19c1.5 0 2.995.079 4.487.236a61.85 61.85 0 0 0-3.892-.594c-.2.117-.398.237-.595.358zm2.875-1.545c-.779.357-1.537.756-2.28 1.188a18.61 18.61 0 0 0-.092-.317 28.6 28.6 0 0 1 2.372-.87v-.001zM15.003 19c.033 1.095.071 2.256.08 3.316.195-.98.4-1.959.615-2.934-.201-.13-.488-.257-.694-.382h-.001zm3.382 2.257a44.885 44.885 0 0 0-2.687-1.875c.188-.016.376-.03.564-.043a43.228 43.228 0 0 1 2.123 1.918zM15.082 19a51.531 51.531 0 0 0-4.156.224c1.27 0 2.256.054 3.546.161.202-.13.403-.259.61-.385zm-3.3 2.258a44.006 44.006 0 0 1 2.69-1.873l.08.366c-.94.47-1.863.973-2.77 1.507z" fill="#FFF"></path><path d="M15.082 16.44c.225.566.423 1.144.609 1.728a26.97 26.97 0 0 1 2.72-.885c.032-.02.064-.044.097-.064-.954.25-1.895.554-2.817.91a27.216 27.216 0 0 0-.609-1.728 25.526 25.526 0 0 0-.61 1.728 27.29 27.29 0 0 0-2.833-.924l.097.065c.926.25 1.84.55 2.737.898.185-.583.383-1.16.61-1.728h-.001z" fill="#C9252C"></path><path d="M13.527 19.449a42.175 42.175 0 0 0-2.488 2.376l.13-.077a40.676 40.676 0 0 1 2.362-2.24l-.004-.06v.001zm.956-.807h-.056a42.12 42.12 0 0 0-.498.056 24.426 24.426 0 0 0-1.724-1.252v.046c.596.394 1.166.819 1.724 1.258l.554-.061c.18.108.35.202.527.308h.072c-.195-.117-.401-.238-.599-.355zm-2.7 2.669a49.548 49.548 0 0 1 2.77-1.512v-.053c-.94.47-1.864.974-2.77 1.512v.053z" fill="#CB4D2C"></path><path d="M15.698 19.38c-.213.965-.422 1.962-.616 2.934v.038c.195-.98.397-1.952.614-2.927a46.42 46.42 0 0 1 2.688 1.887v-.057a45.031 45.031 0 0 0-2.686-1.874v-.001zm.975.076l-.04-.035v.061c.855.756 1.54 1.432 2.337 2.253l.135.08a42.674 42.674 0 0 0-2.432-2.359z" fill="#A71C20"></path><path d="M13.534 18.656c-1.269.17-2.504.39-3.755.672h.198c1.19-.263 2.39-.475 3.598-.635v-.045l-.04.008h-.001z" fill="#CB4D2C"></path><path d="M16.588 18.642c1.284.17 2.488.392 3.75.677h-.208c-1.17-.26-2.35-.47-3.538-.627l-.004-.05zm-.91 0l.053.007c.723-.42 1.466-.805 2.225-1.156v-.039a26.95 26.95 0 0 0-2.28 1.188h.002z" fill="#A71C20"></path><path d="M7.972 22.141l-.071.144c.044.018.13.046.169.064.428-.238.85-.476 1.27-.704a1.13 1.13 0 0 1 .044-.156l-.13-.035c-.428.222-.856.451-1.282.687zm2.848 2.303c-.06.015-.137.015-.198.03-.012-.044-.042-.09-.056-.134.299-.372.601-.74.907-1.106l.196-.04c.014.04.042.108.057.14a75.95 75.95 0 0 0-.906 1.11zm11.273-2.303l.071.144c-.044.018-.13.046-.169.064-.428-.238-.85-.476-1.27-.704a1.298 1.298 0 0 0-.044-.156l.13-.035c.428.222.856.451 1.282.687zm-2.848 2.303l.198.03c.018-.043.038-.088.056-.134-.299-.372-.601-.74-.907-1.106l-.196-.04a1.596 1.596 0 0 1-.057.14c.306.367.608.737.906 1.11z" fill="#FFF"></path><path d="M7.968 22.217l-.04.079-.027-.011.071-.144c.425-.236.853-.465 1.283-.687l.13.035c-.01.027-.016.052-.025.077-.034-.011-.08-.019-.116-.028-.429.222-.85.442-1.276.679z" fill="#CC612C"></path><path d="M11.661 23.248c.017.023.035.077.053.108l.015-.018c0-.016-.041-.1-.057-.14l-.196.04c-.306.366-.608.735-.907 1.106 0 .016.018.037.023.053.192-.237.606-.756.907-1.113.045-.015.11-.025.162-.036zm10.436-1.031l.04.079.027-.011-.071-.144a51.88 51.88 0 0 0-1.283-.687l-.13.035c.01.027.016.052.024.077.035-.011.081-.019.117-.028.429.222.85.442 1.276.679zm-3.69 1.031c-.017.023-.035.077-.053.108l-.015-.018c.005-.016.041-.1.057-.14l.196.04c.306.366.608.735.907 1.106l-.021.053c-.192-.238-.608-.756-.91-1.113-.047-.015-.111-.025-.161-.036z" fill="#A71C20"></path><path d="M9.16 19.398c.532-2.475 3.827-3.21 5.894-3.21s5.361.726 5.894 3.21c.662 3.081-3.752 4.228-5.894 4.228-2.142 0-6.556-1.153-5.894-4.228zm.216-.018c.5-2.391 3.689-3.1 5.678-3.1 1.99 0 5.178.709 5.678 3.1.622 2.96-3.619 4.053-5.678 4.053-2.059 0-6.296-1.1-5.674-4.059l-.004.006z" fill="#CB4D2C"></path><path d="M9.179 19.468c.532-2.475 3.69-3.223 5.894-3.223s5.36.748 5.894 3.223c.662 3.081-3.752 4.228-5.894 4.228-2.142 0-6.556-1.148-5.894-4.228zm.216-.018c.5-2.39 3.688-3.1 5.678-3.1 1.99 0 5.177.71 5.678 3.101.622 2.96-3.62 4.053-5.678 4.053-2.06 0-6.298-1.095-5.678-4.054z" fill="#FFF"></path><path d="M22.11 19.978l-.967-.148-.129-.03a1.16 1.16 0 0 1-.606-.432c-.135-.216-.46-.59-.639-.824-.245-.324-.808-.618-.89-.75a4.786 4.786 0 0 1-.255-.459.256.256 0 0 0-.272-.158c-.146.01-.159.061-.283.069-.1.003-.2-.004-.299-.02a.883.883 0 0 0-.181-.025c-.078.132-.196.215-.482.215a.168.168 0 0 1-.05-.006c.03.067.297.108.341.108.045 0 .299.07.356.082a.44.44 0 0 1 .28.092c.31.186.77.698.862 1.141.05.245.454.648.502.872-.54-.042-1.257-.414-1.257-.414l-.09-.045a2.118 2.118 0 0 0-.813-.192c-.933-.087-1.283-.216-1.375-.249-.192-.067-.165-.1-.322-.108h-.084a.382.382 0 0 1-.08.108 2.617 2.617 0 0 0-.436.534.31.31 0 0 1-.184.124c.177-.042.524-.14.524-.14.327-.045.83.109.83.109.166.043.329.097.488.162 0 0 1.324.42 1.648.667.213.162 1.314.59 1.605.695.343.152.677.321 1.003.51.182.161 1.244.647 1.537.747.363-.62.505-1.353.401-2.07l-.684-.165h.001z" fill="#4F264A" fillRule="nonzero" opacity=".45"></path><path d="M21.622 18.832a.881.881 0 0 0-.201.025l-.264.162c-.033.057.087.13.087.13s.332.023.331.109c.245.136.766.152 1.037.165a4.177 4.177 0 0 0-.257-.543c-.016 0-.223-.06-.733-.048zm-1.966 3.374s-.063-.084-.4-.085c-.215 0 .092.527.092.527.194.135.889.341 1.037.361.148.021.627.169.627.169.174.026.345.065.512.117.19-.181.366-.377.527-.585-.587-.014-1.42-.024-2.051-.368-.065-.192-.344-.136-.344-.136z" fill="#4F264A" fillRule="nonzero" opacity=".45"></path><path d="M12.24 11.89c.024-.252-.01-.23.144-.465.144-.216.074-.324.2-.514.3-.413.62-.81.958-1.188.234-.247.552-1.08.627-1.375.014-.043.036-.063.045-.027.05.559.126 1.025.104 1.574-.04 1.153-.01 1.032.451 2.109.038-.432.14-1.062.382-1.274-.283 1.123-.301 2.722-.331 3.214-.016.251-.08.648.035.876.034.188.048.38.045.57l.007 1.97-.166.863c-.11.321-.32.6-.596.798-.072.042-.243.137-.243.216 0 .136.844.391 1.037.081.122-.197.268-.376.436-.533.266-.266-.03-.54.006-.898.061-.625.32-1.168.404-1.759a1.97 1.97 0 0 0-.114-1.074l.113-1.129c.133.247.253.501.36.761.207.457.213.503.577.877.13.13.244.275.342.432.172.283.29.565.275.903-.126.239-.18.224-.343.368.005.008-.045.14.11.14.548 0 .48-.3.688-.686.134-.25.519-.63.097-.762a.58.58 0 0 1-.334-.259c-.294-.51-.304-.496-.456-.965a1.55 1.55 0 0 0-.502-.7 3.03 3.03 0 0 1-.232-.888 64.37 64.37 0 0 0-.174-1.262c.214-.886.324-1.072-.04-1.923a1.583 1.583 0 0 1-.132-.635c.035-.43.134-.85.295-1.248a.5.5 0 0 1 .222-.3c.088.344.393 1.049.569 1.222-.001.139.018.277.058.41.223.556.543 1.063.703 1.649.055.202-.019.3-.074.462a1.51 1.51 0 0 0 .013.472l.075.043c-.02.088-.044.175-.073.26.01.078.104.124.104.124.122-.028.296-.49.29-.674a3.882 3.882 0 0 0-.165-.985c-.18-.958-.076-1.22-.59-2.147l-.293-.756c-.036-.385-.184-1.266-.658-1.423-.08-.027-.16-.032-.28-.073-.623-.21-.623-.706-.45-1.248.15-.483.17-1.052-.512-1.111-.42-.037-.55.309-.64.707 0 .043.024.691.117.83.18.14.28.099.27.449-.01.293-.19.467-.433.556-.396.146-.6.23-.733.656-.057.2-.083.407-.079.614v.028c-.13.308-.244.622-.343.942-.488.51-.726 1.67-1.224 2.16-.329.323-.428.345-.311.847.012.051.17.182.207.2.037.018.156-.072.154-.102h.001z" fill="url(#RABONL2U-c)" fillRule="nonzero"></path></g></svg>
                                                                        <p>Rabobank</p>
                                                                    </li>
                                                                    <li>
                                                                        <svg width="30" height="30" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="30" height="30" rx="8" fill="#D10600"></rect><path fillRule="evenodd" clipRule="evenodd" d="M4.5 10.579c0-3.462 2.868-5.891 6.196-5.891 4.383 0 5.93 3.935 5.93 8.79 0 .04-3.048 2.486-3.048 6.739 0 3.296 1.501 4.347 3.008 4.942l.088.33s-8.237 1.676-8.237-6.12c0-2.81 1.576-4.184 1.576-6.672 0-.8-.72-1.919-2.285-1.919-1.49 0-2.31 1.066-2.31 1.902l-.314.005C4.74 12.068 4.5 11.34 4.5 10.58zm19.281 11.307s-.091-1.49-1.03-1.49c-.84 0-1.304.736-1.304.736s.971.75.971 1.918c0 1.205-2.527 2.39-3.612 2.59a4.855 4.855 0 0 1-.393-1.871c0-2.646 1.83-5.113 4.79-5.113 2.19 0 .944 3.305.944 3.305l-.366-.075zm-3.633-7.556a.91.91 0 1 1-1.818 0 .91.91 0 1 1 1.818 0zm2.616-3.2s.529-1.91-.536-3.823l-.327.125c.112 1.685-.417 2.895-.417 2.888-.478-.179-1.462-.108-1.462-.108s-.592-1.163-.582-2.786l-.322-.115c-.487.87-.815 2.168-.67 3.12.274 1.76.49 1.276 2.435 2.823 2.112 1.681 1.204 3.549 1.204 3.549l.503.107s1.04-1.97 1.04-3.419c0-1.45-.866-2.362-.866-2.362z" fill="#fff"></path></svg>
                                                                        <p>ASN Bank</p>
                                                                    </li>
                                                                    <li>
                                                                        <svg width="30" height="30" fill="none" xmlns="http://www.w3.org/2000/svg"><path fillRule="evenodd" clipRule="evenodd" d="M2.727 28.017V1.983A7.981 7.981 0 0 0 0 8v14a7.98 7.98 0 0 0 2.727 6.017z" fill="#008F42"></path><path fillRule="evenodd" clipRule="evenodd" d="M5.457 29.587V.413A7.99 7.99 0 0 0 2.73 1.98v26.038a7.992 7.992 0 0 0 2.727 1.568z" fill="#00AB47"></path><path fillRule="evenodd" clipRule="evenodd" d="M8.187 30V0H8c-.888 0-1.742.145-2.54.412v29.176A7.989 7.989 0 0 0 8 30h.187z" fill="#52C638"></path><path fillRule="evenodd" clipRule="evenodd" d="M10.917 30V0H8.19v30h2.727z" fill="#96C65A"></path><path fillRule="evenodd" clipRule="evenodd" d="M10.92 0h2.727v30H10.92V0z" fill="#00C4B5"></path><path fillRule="evenodd" clipRule="evenodd" d="M13.65 0h2.727v30H13.65V0z" fill="#00A2DC"></path><path fillRule="evenodd" clipRule="evenodd" d="M16.38 0h2.727v30H16.38V0z" fill="#0A6CB8"></path><path fillRule="evenodd" clipRule="evenodd" d="M21.837 30V0H19.11v30h2.727z" fill="#46597D"></path><path fillRule="evenodd" clipRule="evenodd" d="M21.84 0v30H22c.898 0 1.761-.148 2.567-.42V.42A7.99 7.99 0 0 0 22 0h-.16z" fill="#FF001A"></path><path fillRule="evenodd" clipRule="evenodd" d="M24.57.422v29.156a7.993 7.993 0 0 0 2.727-1.583V2.005A7.992 7.992 0 0 0 24.57.422z" fill="#FF6B00"></path><path fillRule="evenodd" clipRule="evenodd" d="M27.3 2.007v25.986A7.98 7.98 0 0 0 30 22V8a7.98 7.98 0 0 0-2.7-5.993z" fill="#F7C000"></path><path d="M13.313 13.125h.937v2.524c0 1.194-1.05 2.164-2.344 2.164-1.293 0-2.344-.97-2.344-2.164v-2.524h.938v2.524c0 .73.688 1.322 1.406 1.322.719 0 1.406-.592 1.406-1.322v-2.524zm5.625 4.688h.937v-2.524c0-1.195-1.05-2.164-2.344-2.164-1.293 0-2.343.97-2.343 2.164v2.524h.937v-2.524c0-.73.688-1.323 1.406-1.323.719 0 1.407.593 1.407 1.323v2.524zm-15-.134H3V10.312h.938v2.68c.47-.337 1.242-.537 1.874-.537 1.553 0 2.813 1.2 2.813 2.679 0 1.478-1.26 2.678-2.813 2.678-.672 0-1.39-.225-1.875-.6v.467zm1.804-4.286c-.97 0-1.758.78-1.758 1.74a1.75 1.75 0 0 0 1.758 1.742c.97 0 1.758-.78 1.758-1.741a1.75 1.75 0 0 0-1.758-1.741zm19.758-.79h.938v2.41c-.002.045 0 .09 0 .134l-.004 4.54H25.5V17.29c-.47.336-1.242.536-1.875.536-1.552 0-2.813-1.2-2.813-2.679 0-1.478 1.26-2.678 2.813-2.678.672 0 1.391.225 1.875.6v-.466zm-1.805 4.286c.97 0 1.758-.78 1.758-1.742a1.75 1.75 0 0 0-1.758-1.74c-.97 0-1.758.78-1.758 1.74 0 .961.788 1.742 1.758 1.742z" fill="#fff"></path></svg>
                                                                        <p>bunq</p>
                                                                    </li>
                                                                    <li>
                                                                        <svg width="30" height="30" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient x1="0%" y1="100.35%" x2="100.35%" y2="-2.02%" id="HANDNL2A-a"><stop stopColor="#0063A5" offset="0%"></stop><stop stopColor="#008DC7" offset="100%"></stop></linearGradient></defs><g fill="none" fillRule="evenodd"><path d="M22 0H8a8 8 0 0 0-8 8v14a8 8 0 0 0 8 8h14a8 8 0 0 0 8-8V8a8 8 0 0 0-8-8z" fill="url(#HANDNL2A-a)" fillRule="nonzero"></path><path fill="#FFF" fillRule="nonzero" d="M9.354 6.563v6.525h6.87V6.563h3.75v16.983h-3.75v-7.337h-6.87v7.337H5.625V6.563z"></path><path d="M30 11.026a15.191 15.191 0 0 0-1.92-.126c-.669 0-1.358.06-2.027.202a5.776 5.776 0 0 0-1.824.69 3.768 3.768 0 0 0-1.337 1.256c-.345.527-.547 1.195-.608 1.945h3.425c.06-.669.284-1.155.669-1.439.385-.283.891-.425 1.56-.425.284 0 .568.02.831.06.263.041.486.122.689.244.247.163.436.398.542.674v-3.081zm0 4.34a.8.8 0 0 1-.197.235c-.223.183-.527.324-.912.426a7.813 7.813 0 0 1-1.318.223c-.486.04-.993.101-1.5.182-.506.081-.992.183-1.5.324a4.495 4.495 0 0 0-1.337.608 3.327 3.327 0 0 0-.952 1.095c-.243.446-.365 1.033-.365 1.722 0 .629.101 1.176.324 1.642.223.446.507.83.892 1.135.385.304.83.527 1.337.669.535.15 1.087.224 1.642.223.75 0 1.5-.102 2.23-.325A4.456 4.456 0 0 0 30 22.584V19.63a2.444 2.444 0 0 1-.319.896c-.201.326-.48.597-.81.79-.365.223-.872.325-1.54.325-.285 0-.548-.041-.791-.081a1.64 1.64 0 0 1-.67-.244 1.29 1.29 0 0 1-.445-.486 1.8 1.8 0 0 1-.162-.77c0-.325.06-.588.162-.79.122-.203.264-.365.446-.507.182-.142.405-.243.648-.325.244-.08.507-.141.75-.182l.412-.062.399-.06.027-.004c.254-.039.508-.078.743-.117l.199-.047c.168-.04.33-.077.49-.136a2.56 2.56 0 0 0 .461-.22v-2.244z" fill="#FFF"></path></g></svg>
                                                                        <p>Handelsbanken</p>
                                                                    </li>
                                                                    <li>
                                                                        <svg width="30" height="30" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M22 0H8a8 8 0 0 0-8 8v14a8 8 0 0 0 8 8h14a8 8 0 0 0 8-8V8a8 8 0 0 0-8-8z" fill="#00374E"></path><path fillRule="evenodd" clipRule="evenodd" d="M12.94 12.846c-.812 0-1.327.397-1.635.883h-.024l.011-.784h-1.56c-.138 0-.27.167-.27.326v4.797c0 .16.131.317.277.317h1.553v-2.692c0-.784.219-1.402 1.007-1.402.756 0 .87.54.87 1.258v2.515c0 .167.13.32.268.32H15v-3.21c0-1.313-.423-2.328-2.06-2.328zm11.385 4.397c-.741 0-1.247-.572-1.247-1.41 0-.836.506-1.409 1.247-1.409.742 0 1.236.573 1.236 1.41s-.494 1.409-1.236 1.409zm.232-4.263c-.677 0-1.143.24-1.424.495.006-.185.012-.458.012-.727V11H21.71c-.127 0-.248.161-.248.308v6.655c0 .159.118.306.252.306h1.348l-.007-.426c.384.369.909.542 1.492.542 1.505 0 2.453-1.18 2.453-2.708 0-1.527-.948-2.697-2.443-2.697zm-16.182.128H6.58a.536.536 0 0 0-.364.168L4.74 14.853V11H3.256c-.13 0-.256.162-.256.31v6.769c0 .151.104.306.258.306H4.74V16.79l.478-.46 1.19 1.857c.111.151.265.198.443.198h1.686l-2.121-3.297 1.958-1.98zm10.622 3.689c-.098.184-.378.487-.796.487-.426 0-.726-.25-.726-.66 0-.398.28-.625.755-.647l.767-.054v.874zm.97-3.336c-.38-.388-.893-.615-1.785-.615-.763 0-1.418.23-1.846.438-.11.056-.154.222-.11.35l.318.875c.242-.173.853-.476 1.464-.476.62 0 .979.293.979.853v.12l-1.202.054c-.882.043-1.862.442-1.862 1.63 0 1.187.893 1.695 1.736 1.695.776 0 1.163-.454 1.367-.68l.02.362c.006.1.092.199.212.199h1.28v-3.001c0-.897-.203-1.415-.572-1.804z" fill="#fff"></path></svg>
                                                                        <p>Knab</p>
                                                                    </li>
                                                                    <li>
                                                                        <svg width="30" height="30" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 .5h14A7.5 7.5 0 0 1 29.5 8v14a7.5 7.5 0 0 1-7.5 7.5H8A7.5 7.5 0 0 1 .5 22V8A7.5 7.5 0 0 1 8 .5z" fill="#fff" stroke="#EEE"></path><path fillRule="evenodd" clipRule="evenodd" d="M11.725 21.987a3.711 3.711 0 1 1 0-7.423 3.711 3.711 0 0 1 0 7.423zm-.023-9.406a5.702 5.702 0 1 0 0 11.404 5.702 5.702 0 0 0 0-11.404z" fill="#D30D48"></path><path fillRule="evenodd" clipRule="evenodd" d="M16.352 19.282a5.634 5.634 0 1 1 0-11.269 5.634 5.634 0 0 1 0 11.27zM16.367 6a7.633 7.633 0 1 0 0 15.266 7.633 7.633 0 0 0 0-15.266z" fill="#2AAACD"></path><path fillRule="evenodd" clipRule="evenodd" d="M10.812 14.678a3.711 3.711 0 0 1 4.51 4.51 5.67 5.67 0 0 0 2.009.01 5.702 5.702 0 0 0-6.525-6.546 5.672 5.672 0 0 0 .006 2.025z" fill="#F9BA30"></path><path fillRule="evenodd" clipRule="evenodd" d="M16.397 19.282l-.045.001a5.634 5.634 0 0 1-5.634-5.635h-.001v-.045a.992.992 0 1 0-1.982 0v.03a7.633 7.633 0 0 0 7.632 7.632h.03a.992.992 0 1 0 0-1.983z" fill="#55548F"></path></svg>
                                                                        <p>Moneyou</p>
                                                                    </li>
                                                                    <li>
                                                                        <svg width="30" height="30" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M22 0H8a8 8 0 0 0-8 8v14a8 8 0 0 0 8 8h14a8 8 0 0 0 8-8V8a8 8 0 0 0-8-8z" fill="#D01A21"></path><path fillRule="evenodd" clipRule="evenodd" d="M8.996 10.97c-.245-.488-.668-.976-1.28-1.08.011 0 .056-.01.089-.01C8.94 9.735 9.43 8.957 9.43 8.168c0-.674-.4-1.732-2.248-1.732H5.256c-.134 0-.256.104-.256.228v6.525c0 .125.122.229.256.229h.534c.134 0 .245-.104.245-.229v-2.915h.523c.757 0 1.102.436 1.358.882.312.54.523 1.287.813 2.096.044.135.145.166.267.166h.735c.2 0 .234-.114.167-.28-.39-1.006-.768-1.91-.902-2.168zm-.623-2.667c0 .716-.512 1.152-1.336 1.152H6.035V7.297H7.16c.802 0 1.214.446 1.214 1.006zm5.514 2.614c0-1.909-.89-2.583-2.026-2.583-1.225 0-2.048.986-2.048 2.552 0 1.702.857 2.584 2.382 2.584.645 0 1.024-.094 1.38-.24.122-.051.19-.155.145-.28l-.123-.331c-.044-.114-.133-.197-.3-.145-.323.083-.624.176-1.047.176-1.002 0-1.335-.54-1.38-1.504h2.772c.133 0 .245-.104.245-.229zm-1.036-.487H10.87c.111-1.079.612-1.338 1.024-1.338.69 0 .935.674.957 1.338zm4.206 2.106h-.923c-.401 0-.58-.062-.58-.28 0-.114.09-.228.201-.322.167.042.356.063.545.063 1.236 0 2.026-.696 2.026-1.837 0-.353-.167-.695-.4-.996l.367-.041c.134-.01.223-.083.223-.218v-.27c0-.135-.09-.239-.245-.239h-1.28c-.234 0-.457-.051-.69-.051-1.192 0-2.004.653-2.004 1.815 0 .685.311 1.203.801 1.505-.267.228-.49.498-.49.86 0 .602.568.81 1.225.81h1.08c.6 0 .834.228.834.653 0 .457-.534.737-1.213.737-.523 0-1.102-.093-1.19-.643-.023-.135-.112-.208-.246-.218l-.556-.021c-.134 0-.245.094-.245.229.011 1.12 1.135 1.452 2.204 1.452 1.024 0 2.215-.436 2.215-1.64 0-.86-.646-1.348-1.659-1.348zm.245-2.397c0 .934-.478 1.131-.99 1.131-.535 0-.98-.166-.98-1.13 0-.924.445-1.09.98-1.09.545 0 .99.176.99 1.09zm3.043-3.496c0-.353-.3-.643-.679-.643-.39 0-.69.29-.69.643 0 .363.3.654.69.654.378 0 .68-.29.68-.654zm-.178 6.546V8.625c0-.125-.123-.229-.245-.229h-.5c-.134 0-.257.104-.257.229v4.564c0 .125.123.229.256.229h.501c.122 0 .245-.104.245-.229zM25 10.907c0-1.587-.824-2.573-2.16-2.573-1.335 0-2.17.975-2.17 2.573 0 1.608.835 2.563 2.17 2.563 1.347 0 2.16-.975 2.16-2.563zm-1.057 0c0 1.256-.423 1.774-1.125 1.774-.69 0-1.113-.518-1.113-1.774 0-1.234.423-1.774 1.113-1.774.702 0 1.125.54 1.125 1.774zM8.164 20.238c.899-.256 1.276-.789 1.276-1.588 0-1.055-.744-1.78-2.031-1.78H5.255c-.133 0-.255.107-.255.235v6.703c0 .127.122.234.255.234H7.52c1.398 0 2.242-.735 2.242-2.014 0-.927-.533-1.673-1.598-1.79zm.244-1.45c0 .661-.425 1.077-1.263 1.077H6.039v-2.142h1.184c.793 0 1.185.384 1.185 1.066zm.267 3.134c0 .777-.457 1.246-1.228 1.246H6.039v-2.504h1.195c.906 0 1.441.533 1.441 1.258zm5.505 1.886l-.067-.31c-.067-.32-.056-.607-.056-.873v-1.854c0-.864-.145-1.95-1.921-1.95-.48 0-1.028.117-1.352.202-.135.032-.19.138-.156.277l.088.394c.034.139.146.213.292.17.3-.074.636-.18 1.071-.18.794 0 .972.287.972.905v.256l-1.228.075c-.905.064-1.676.511-1.676 1.651 0 .96.548 1.524 1.44 1.524.616 0 1.196-.213 1.543-.735l.078.469c.022.149.123.213.257.213h.514c.146 0 .223-.096.201-.234zm-1.129-1.471c0 .394-.457.98-1.172.98-.413 0-.704-.234-.704-.746 0-.799.491-.99 1.173-1.012l.703-.021v.799zm5.528-2.184c0-.8-.537-1.332-1.374-1.332-.593 0-1.061.213-1.531.735l-.112-.48c-.033-.138-.111-.192-.268-.192h-.335c-.134 0-.257.096-.257.224v4.7c0 .127.123.234.257.234h.514a.243.243 0 0 0 .246-.234v-2.899c0-.714.525-1.257 1.206-1.257.458 0 .637.223.637.714v3.442c0 .127.123.234.257.234h.514a.243.243 0 0 0 .246-.234v-3.655zm4.48 3.612l-1.831-2.664 1.497-1.95c.09-.117.033-.267-.123-.267h-.714a.36.36 0 0 0-.325.181l-1.352 1.972V16.71c0-.139-.111-.235-.245-.235h-.504c-.133 0-.245.096-.245.235v7.097c0 .127.112.234.245.234h.504a.242.242 0 0 0 .245-.234V21.26l1.62 2.579c.078.127.179.202.302.202h.793c.157 0 .213-.16.134-.277z" fill="#fff"></path></svg>
                                                                        <p>RegioBank</p>
                                                                    </li>
                                                                    <li>
                                                                        <svg width="30" height="30" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="30" height="30" rx="8" fill="#21262E"></rect><path d="M14.42 8.37c-.298 1.997-.536 3.68-.964 6.298 1.157-.024 3.785.416 4.363-3.163.479-2.978-1.655-3.253-3.4-3.134z" fill="#fff"></path><path d="M24.445 22.77c.45.956-.003 2.165-.943 2.652-.54.28-1.155.54-1.771.685a15.53 15.53 0 0 1-3.096.384c-1.67 0-3.34-.911-4.062-2.02-.187.383-.86 1.151-2.038 1.513-.745.23-1.643.507-3.517.505-1.68-.001-2.637-.727-3.144-1.337-.805-.97-.924-2.2-.766-3.03l.008-.04c.632-3.033 1.5-8.546 1.899-11.31l.004-.027c.11-.766.22-1.532.286-2.303.033-.39.055-.78.059-1.17.004-.368-.032-.736-.028-1.105.004-.347.062-.7.233-1.007.277-.497.786-.888 1.323-1.059.59-.187 1.207-.263 1.82-.33a50.884 50.884 0 0 1 1.883-.18A33.494 33.494 0 0 1 14.9 3.51c1.317 0 2.641.021 3.937.275 1.322.26 2.656.757 3.699 1.627.714.596 1.281 1.349 1.706 2.172.375.725.526 1.488.616 2.073a8.266 8.266 0 0 1-.124 3.145 6.735 6.735 0 0 1-.957 2.12c-.497.736-1.133 1.403-1.946 2.039.495 1.368 1.784 4.043 2.615 5.81zM9.667 6.306c.031.507.036 1.02.013 1.53-.023.52-.07 1.039-.13 1.556-.06.515-.133 1.029-.206 1.542l-.024.168c-.231 1.606-.473 3.21-.729 4.81-.267 1.682-.568 3.357-.868 5.033-.14.78-.453 1.782-.025 2.528.382.665 1.36.746 2.042.679 2.484-.242 2.878-.769 2.878-.769-.495-.523-.065-2.909.434-6.117h1.766l1.847 5.331s.545 1.652 2.06 1.572c1.939-.102 3.365-.525 3.57-.759-.597-.319-2.303-4.391-3.234-7.382.45-.261.887-.546 1.298-.865.564-.437 1.084-.942 1.484-1.535.287-.425.506-.894.631-1.392.067-.263.104-.537.127-.806a5.925 5.925 0 0 0-.046-1.423c-.07-.451-.174-.954-.384-1.362-.394-.763-.95-1.428-1.71-1.853-1.28-.717-2.754-.854-4.193-.93a27.818 27.818 0 0 0-4.675.146c-.173.02-1.933.168-1.926.298z" fill="#fff"></path></svg>
                                                                        <p>Revolut</p>
                                                                    </li>
                                                                    <li>
                                                                        <svg width="30" height="30" xmlns="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/1999/xlink"><defs><path id="SNSBNL2A-a" d="M0 3l9.61 2.755L3.71.944z"></path><linearGradient x1="41.164%" y1="48.735%" x2="64.134%" y2="61.857%" id="SNSBNL2A-b"><stop stopColor="#EF4130" offset="0%"></stop><stop stopColor="#C53526" offset="91.875%"></stop><stop stopColor="#C53526" offset="100%"></stop></linearGradient><path id="SNSBNL2A-d" d="M6.29 5.056L10 3 .39.244z"></path><linearGradient x1="-.395%" y1="42.782%" x2="57.225%" y2="75.704%" id="SNSBNL2A-e"><stop stopColor="#D4D3EA" offset="0%"></stop><stop stopColor="#8685C0" offset="89.494%"></stop><stop stopColor="#8685C0" offset="100%"></stop></linearGradient><path d="M.756.39l.954 7.554 3.483-4.89A9.951 9.951 0 0 0 .756.39z" id="SNSBNL2A-g"></path><linearGradient x1="65.612%" y1="57.105%" x2="25.485%" y2="50.789%" id="SNSBNL2A-h"><stop stopColor="#7474B6" offset="0%"></stop><stop stopColor="#7474B6" offset="9.533%"></stop><stop stopColor="#BBB9DD" offset="100%"></stop></linearGradient><path d="M.756.39l.181.333a9.666 9.666 0 0 1 4.004 2.455l.252-.125A9.951 9.951 0 0 0 .756.39z" id="SNSBNL2A-j"></path><linearGradient x1="103.216%" y1="69.299%" x2="42.76%" y2="13.751%" id="SNSBNL2A-k"><stop stopColor="#7474B6" offset="0%"></stop><stop stopColor="#7474B6" offset="9.533%"></stop><stop stopColor="#BBB9DD" offset="100%"></stop></linearGradient><path d="M.71 4.943l5.992-.362A10.054 10.054 0 0 0 4.192.053L.71 4.943z" id="SNSBNL2A-m"></path><linearGradient x1="33.944%" y1="27.289%" x2="66.234%" y2="95.828%" id="SNSBNL2A-n"><stop stopColor="#EF4130" offset="0%"></stop><stop stopColor="#A82B1C" offset="99.659%"></stop><stop stopColor="#A82B1C" offset="100%"></stop></linearGradient><path d="M.128.374a9.777 9.777 0 0 1 2.268 4.09l.306.117A10.054 10.054 0 0 0 .192.053L.129.374z" id="SNSBNL2A-p"></path><linearGradient x1="34.313%" y1="2.215%" x2="83.98%" y2="51.119%" id="SNSBNL2A-q"><stop stopColor="#EF4130" offset="0%"></stop><stop stopColor="#EF4130" offset="3.105%"></stop><stop stopColor="#9E2718" offset="100%"></stop></linearGradient><path d="M.71.944l5.9 4.811A9.95 9.95 0 0 0 6.702.581L.71.944z" id="SNSBNL2A-s"></path><linearGradient x1="33.293%" y1="10.185%" x2="59.09%" y2="96.347%" id="SNSBNL2A-t"><stop stopColor="#EEEDF7" offset="0%"></stop><stop stopColor="#AFAED7" offset="93.388%"></stop><stop stopColor="#AFAED7" offset="100%"></stop></linearGradient><path d="M.465.73a9.68 9.68 0 0 1-.043 4.7l.188.325A9.95 9.95 0 0 0 .702.581L.465.73z" id="SNSBNL2A-v"></path><linearGradient x1="46.98%" y1="45.23%" x2="52.801%" y2="45.336%" id="SNSBNL2A-w"><stop stopColor="#FFF" offset="0%"></stop><stop stopColor="#AFAED7" offset="85.447%"></stop><stop stopColor="#AFAED7" offset="100%"></stop></linearGradient><path d="M.39 5.244l7.553-.954L3.054.807A9.962 9.962 0 0 0 .39 5.244z" id="SNSBNL2A-y"></path><linearGradient x1="15.77%" y1="44.255%" x2="77.488%" y2="74.31%" id="SNSBNL2A-z"><stop stopColor="#FFEF96" offset="0%"></stop><stop stopColor="#FFEF96" offset="11.33%"></stop><stop stopColor="#FFD540" offset="100%"></stop></linearGradient><path d="M.054 2.807L4.943 6.29 4.581.297a10.047 10.047 0 0 0-4.527 2.51z" id="SNSBNL2A-B"></path><linearGradient x1="17.69%" y1="35.588%" x2="97.471%" y2="59.278%" id="SNSBNL2A-C"><stop stopColor="#F8A084" offset="0%"></stop><stop stopColor="#F8A084" offset="3.221%"></stop><stop stopColor="#EF402F" offset="100%"></stop></linearGradient><path d="M.054 2.807l.32.065A9.762 9.762 0 0 1 4.464.605l.117-.307A10.048 10.048 0 0 0 .054 2.807z" id="SNSBNL2A-E"></path><linearGradient x1="-1.538%" y1="66.922%" x2="49.109%" y2="15.505%" id="SNSBNL2A-F"><stop stopColor="#F8A084" offset="0%"></stop><stop stopColor="#F8A084" offset="3.221%"></stop><stop stopColor="#EF402F" offset="100%"></stop></linearGradient><path d="M.581.297L.943 6.29 5.756.39A9.976 9.976 0 0 0 .582.297z" id="SNSBNL2A-H"></path><linearGradient x1="12.005%" y1="36.823%" x2="75.996%" y2="51.882%" id="SNSBNL2A-I"><stop stopColor="#426AB3" offset="0%"></stop><stop stopColor="#143F90" offset="96.787%"></stop><stop stopColor="#143F90" offset="100%"></stop></linearGradient><path d="M.808.946A9.96 9.96 0 0 0 5.244 3.61l-.18-.333A9.67 9.67 0 0 1 1.058.821l-.25.125z" id="SNSBNL2A-K"></path><linearGradient x1="-2.613%" y1="40.513%" x2="84.749%" y2="80.372%" id="SNSBNL2A-L"><stop stopColor="#5563A4" offset="0%"></stop><stop stopColor="#133C8B" offset="63.846%"></stop><stop stopColor="#133C8B" offset="100%"></stop></linearGradient><path d="M.298.42a10.032 10.032 0 0 0 2.51 4.526L6.29.056.298.42z" id="SNSBNL2A-N"></path><linearGradient x1="46.515%" y1="17.403%" x2="51.376%" y2="85.491%" id="SNSBNL2A-O"><stop stopColor="#FFF" offset="0%"></stop><stop stopColor="#D1D0E8" offset="84.738%"></stop><stop stopColor="#D1D0E8" offset="100%"></stop></linearGradient><path d="M.298.42a10.043 10.043 0 0 0 2.51 4.527l.064-.32A9.785 9.785 0 0 1 .603.535L.298.42z" id="SNSBNL2A-Q"></path><linearGradient x1="28.157%" y1="38.979%" x2="74.313%" y2="46.502%" id="SNSBNL2A-R"><stop stopColor="#FFF" offset="0%"></stop><stop stopColor="#C7C6E4" offset="85.64%"></stop><stop stopColor="#C7C6E4" offset="100%"></stop></linearGradient><path d="M.057 1.71l4.89 3.483A9.939 9.939 0 0 0 7.61.756h-.002l-7.55.954z" id="SNSBNL2A-T"></path><linearGradient x1="2.588%" y1="42.028%" x2="67.949%" y2="73.848%" id="SNSBNL2A-U"><stop stopColor="#C7C6E4" offset="0%"></stop><stop stopColor="#7474B6" offset="89.327%"></stop><stop stopColor="#7474B6" offset="100%"></stop></linearGradient><path d="M.057.71l.362 5.992a10.045 10.045 0 0 0 4.527-2.509L.056.71z" id="SNSBNL2A-W"></path><linearGradient x1="23.649%" y1="-5.893%" x2="77.656%" y2="70.43%" id="SNSBNL2A-X"><stop stopColor="#EF4130" offset="0%"></stop><stop stopColor="#B12E20" offset="97.148%"></stop><stop stopColor="#B12E20" offset="100%"></stop></linearGradient><path d="M.536 2.395l-.117.306A10.054 10.054 0 0 0 4.946.194l-.32-.066a9.767 9.767 0 0 1-4.09 2.267z" id="SNSBNL2A-Z"></path><linearGradient x1="-2.092%" y1="67.102%" x2="48.524%" y2="15.698%" id="SNSBNL2A-aa"><stop stopColor="#EF4130" offset="0%"></stop><stop stopColor="#EF4130" offset="3.105%"></stop><stop stopColor="#9E2718" offset="100%"></stop></linearGradient><path d="M.244 6.611a9.963 9.963 0 0 0 5.175.091L5.057.71.244 6.61z" id="SNSBNL2A-ac"></path><linearGradient x1="43.178%" y1="42.634%" x2="93.975%" y2="84.423%" id="SNSBNL2A-ad"><stop stopColor="#F8ABA6" offset="0%"></stop><stop stopColor="#F48594" offset="98.981%"></stop><stop stopColor="#F48594" offset="100%"></stop></linearGradient><path d="M.245.611a9.962 9.962 0 0 0 5.174.091l-.15-.238A9.67 9.67 0 0 1 .57.422L.245.611z" id="SNSBNL2A-af"></path><linearGradient x1="1.266%" y1="55.729%" x2="101.258%" y2="55.729%" id="SNSBNL2A-ag"><stop stopColor="#F8ABA6" offset="0%"></stop><stop stopColor="#F37B90" offset="99.197%"></stop><stop stopColor="#F37B90" offset="100%"></stop></linearGradient></defs><g fill="none" fillRule="evenodd"><path d="M22 0H8a8 8 0 0 0-8 8v14a8 8 0 0 0 8 8h14a8 8 0 0 0 8-8V8a8 8 0 0 0-8-8z" fill="#4858BB" fillRule="nonzero"></path><path fill="#FFF" d="M14.819 13.699l2.833 4.713-5.277 5.85z"></path><g transform="translate(15 12)"><mask id="SNSBNL2A-c" fill="#fff"><use xlink="#SNSBNL2A-a"></use></mask><path fill="url(#SNSBNL2A-b)" mask="url(#SNSBNL2A-c)" d="M0 3l9.61 2.755L3.71.944"></path></g><path fill="#EF4130" d="M18.71 12.943l5.9 4.812-.587-.307-5.326-4.344"></path><path fill="#F58769" d="M15 15l3.71-2.057-.014.161-3.349 1.856"></path><path fill="#BC3123" d="M24.61 17.756L15 15l.347-.04 8.676 2.488"></path><path fill="#F8ABA6" d="M15 15l3.71-2.057-.954-7.553"></path><path fill="#D37482" d="M17.756 5.39L15 15l.218-.274 2.487-8.676"></path><path fill="#D79592" d="M18.71 12.943l-.954-7.553-.051.66.862 6.82"></path><path fill="#FBCBC5" d="M15 15l3.71-2.057-.143-.074-3.35 1.857"></path><path fill="#FFF" d="M17.057 18.71l-4.813 5.9.307-.588 4.345-5.327M15 15l2.057 3.71-.16-.015-1.857-3.348"></path><path fill="#D3D2EA" d="M12.244 24.61l2.757-9.611.039.347-2.488 8.676"></path><path fill="#293896" d="M15 15l2.057 3.71 7.553-.955"></path><path fill="#464EA1" d="M24.61 17.756L15 15l.274.218 8.676 2.487"></path><path fill="#1F2879" d="M17.057 18.71l7.554-.954-.661-.051-6.82.861"></path><path fill="#8785C0" d="M15 15l2.057 3.71.074-.143-1.857-3.35"></path><g transform="translate(5 12)"><mask id="SNSBNL2A-f" fill="#fff"><use xlink="#SNSBNL2A-d"></use></mask><path fill="url(#SNSBNL2A-e)" mask="url(#SNSBNL2A-f)" d="M6.29 5.056L10 3 .39.244"></path></g><path fill="#B0AED7" d="M11.29 17.057l-5.9-4.813.587.307 5.327 4.346"></path><path fill="#FFDE17" d="M15 14.999l-3.71 2.057.954 7.554"></path><path fill="#D3B382" d="M12.244 24.61l2.757-9.611-.219.274-2.486 8.675"></path><path fill="#E8C446" d="M11.29 17.056l.954 7.554.051-.662-.862-6.819"></path><path fill="#EF4130" d="M15 15l-2.056-3.71 4.812-5.9"></path><path fill="#F16246" d="M12.944 11.29l4.812-5.9-.307.588-4.345 5.326"></path><path fill="#D93B2B" d="M15 15l-2.056-3.71.16.014 1.857 3.349"></path><path fill="#C53526" d="M17.756 5.39L15 15l-.04-.348 2.488-8.675"></path><path fill="#EEEDF7" d="M15 15l-2.056-3.71-7.554.954"></path><path fill="#D3D2EA" d="M12.944 11.29l-7.554.955.661.05 6.82-.862"></path><g transform="translate(17 5)"><mask id="SNSBNL2A-i" fill="#fff"><use xlink="#SNSBNL2A-g"></use></mask><path d="M.756.39l.954 7.554 3.483-4.89A9.951 9.951 0 0 0 .756.39" fill="url(#SNSBNL2A-h)" mask="url(#SNSBNL2A-i)"></path></g><path d="M17.756 5.39l.954 7.553.088-.355-.896-7.155a9.36 9.36 0 0 0-.146-.044z" fill="#8785C0"></path><path d="M22.099 7.958l-3.3 4.63-.089.355 3.484-4.89a4.06 4.06 0 0 0-.095-.095z" fill="#9A98CB"></path><g transform="translate(17 5)"><mask id="SNSBNL2A-l" fill="#fff"><use xlink="#SNSBNL2A-j"></use></mask><path d="M.756.39l.181.333a9.666 9.666 0 0 1 4.004 2.455l.252-.125A9.951 9.951 0 0 0 .756.39" fill="url(#SNSBNL2A-k)" mask="url(#SNSBNL2A-l)"></path></g><g transform="translate(18 8)"><mask id="SNSBNL2A-o" fill="#fff"><use xlink="#SNSBNL2A-m"></use></mask><path d="M.71 4.943l5.992-.362A10.054 10.054 0 0 0 4.192.053" fill="url(#SNSBNL2A-n)" mask="url(#SNSBNL2A-o)"></path></g><path d="M22.193 8.054l-3.483 4.89.27-.15 3.307-4.641a5.669 5.669 0 0 0-.094-.1z" fill="#F37557"></path><path d="M24.669 12.448l-5.69.346-.269.15 5.992-.364-.033-.132z" fill="#A82B1C"></path><g transform="translate(22 8)"><mask id="SNSBNL2A-r" fill="#fff"><use xlink="#SNSBNL2A-p"></use></mask><path d="M.128.374a9.777 9.777 0 0 1 2.268 4.09l.306.117A10.054 10.054 0 0 0 .192.053" fill="url(#SNSBNL2A-q)" mask="url(#SNSBNL2A-r)"></path></g><g transform="translate(18 12)"><mask id="SNSBNL2A-u" fill="#fff"><use xlink="#SNSBNL2A-s"></use></mask><path d="M.71.944l5.9 4.811A9.95 9.95 0 0 0 6.702.581" fill="url(#SNSBNL2A-t)" mask="url(#SNSBNL2A-u)"></path></g><path d="M24.702 12.58l-5.992.364.35.111 5.674-.343a3.164 3.164 0 0 0-.032-.132z" fill="#C7C6E3"></path><path d="M24.61 17.756l.04-.141-5.59-4.56-.35-.112" fill="#EEEDF7"></path><g transform="translate(24 12)"><mask id="SNSBNL2A-x" fill="#fff"><use xlink="#SNSBNL2A-v"></use></mask><path d="M.465.73a9.68 9.68 0 0 1-.043 4.7l.188.325A9.95 9.95 0 0 0 .702.581" fill="url(#SNSBNL2A-w)" mask="url(#SNSBNL2A-x)"></path></g><g transform="translate(5 7)"><mask id="SNSBNL2A-A" fill="#fff"><use xlink="#SNSBNL2A-y"></use></mask><path d="M.39 5.244l7.553-.954L3.054.807A9.962 9.962 0 0 0 .39 5.244z" fill="url(#SNSBNL2A-z)" mask="url(#SNSBNL2A-A)"></path></g><path d="M5.39 12.244l7.554-.954-.356-.088-7.155.897c-.014.049-.03.097-.043.145z" fill="#FFCB1F"></path><path d="M7.958 7.901l4.63 3.3.356.089-4.89-3.483c-.032.03-.065.062-.096.094z" fill="#FFDE17"></path><path d="M8.054 7.807a9.954 9.954 0 0 0-2.664 4.437l.333-.181a9.683 9.683 0 0 1 2.455-4.004" fill="#FFFAC2"></path><g transform="translate(8 5)"><mask id="SNSBNL2A-D" fill="#fff"><use xlink="#SNSBNL2A-B"></use></mask><path d="M.054 2.807L4.943 6.29 4.581.297a10.047 10.047 0 0 0-4.527 2.51z" fill="url(#SNSBNL2A-C)" mask="url(#SNSBNL2A-D)"></path></g><path d="M8.055 7.807l4.889 3.483-.15-.27-4.64-3.306a2.2 2.2 0 0 0-.1.093zm4.395-2.475l.344 5.688.15.27-.363-5.993-.132.035z" fill="#EF4130"></path><g transform="translate(8 5)"><mask id="SNSBNL2A-G" fill="#fff"><use xlink="#SNSBNL2A-E"></use></mask><path d="M.054 2.807l.32.065A9.762 9.762 0 0 1 4.464.605l.117-.307A10.048 10.048 0 0 0 .054 2.807z" fill="url(#SNSBNL2A-F)" mask="url(#SNSBNL2A-G)"></path></g><g transform="translate(12 5)"><mask id="SNSBNL2A-J" fill="#fff"><use xlink="#SNSBNL2A-H"></use></mask><path d="M.581.296l.362 5.99L5.756.389A9.98 9.98 0 0 0 .582.296z" fill="url(#SNSBNL2A-I)" mask="url(#SNSBNL2A-J)"></path></g><path d="M12.581 5.298l.362 5.992.113-.35-.343-5.674-.132.032z" fill="#17479E"></path><path d="M17.756 5.39a6.954 6.954 0 0 0-.14-.04l-4.56 5.59-.112.35 4.812-5.9z" fill="#2E3092"></path><path d="M7.808 21.945a9.95 9.95 0 0 0 4.436 2.665l-.954-7.554" fill="#293896"></path><path d="M12.244 24.61l-.954-7.554-.088.356.896 7.155.146.042z" fill="#1F2879"></path><path d="M7.901 22.043l3.3-4.63.089-.357-3.482 4.89.093.096z" fill="#595DA9"></path><g transform="translate(7 21)"><mask id="SNSBNL2A-M" fill="#fff"><use xlink="#SNSBNL2A-K"></use></mask><path d="M.808.946A9.96 9.96 0 0 0 5.244 3.61l-.18-.333A9.67 9.67 0 0 1 1.058.821" fill="url(#SNSBNL2A-L)" mask="url(#SNSBNL2A-M)"></path></g><g transform="translate(5 17)"><mask id="SNSBNL2A-P" fill="#fff"><use xlink="#SNSBNL2A-N"></use></mask><path d="M.298.42a10.032 10.032 0 0 0 2.51 4.526L6.29.056" fill="url(#SNSBNL2A-O)" mask="url(#SNSBNL2A-P)"></path></g><path d="M7.807 21.946l3.483-4.89-.27.15-3.306 4.641.093.099z" fill="#A5A3D1"></path><path d="M5.332 17.55l5.688-.344.27-.15-5.992.363.034.131z" fill="#D1D0E9"></path><g transform="translate(5 17)"><mask id="SNSBNL2A-S" fill="#fff"><use xlink="#SNSBNL2A-Q"></use></mask><path d="M.298.42a10.043 10.043 0 0 0 2.51 4.527l.064-.32A9.785 9.785 0 0 1 .603.535L.298.42" fill="url(#SNSBNL2A-R)" mask="url(#SNSBNL2A-S)"></path></g><path d="M11.29 17.057l-5.992.362a9.957 9.957 0 0 1 .092-5.175" fill="#EF4130"></path><path d="M5.298 17.419l5.992-.363-.35-.112-5.674.343.032.132z" fill="#CF381E"></path><path d="M5.39 12.244l-.039.141 5.59 4.56.349.112-5.9-4.813z" fill="#F37557"></path><path d="M5.578 12.569a9.684 9.684 0 0 0-.042 4.7l-.238.15a9.956 9.956 0 0 1 .091-5.175" fill="#F37557"></path><g transform="translate(17 17)"><mask id="SNSBNL2A-V" fill="#fff"><use xlink="#SNSBNL2A-T"></use></mask><path d="M.057 1.71l4.89 3.483A9.939 9.939 0 0 0 7.61.756h-.002" fill="url(#SNSBNL2A-U)" mask="url(#SNSBNL2A-V)"></path></g><path d="M24.61 17.756l-7.553.954.356.089 7.155-.897.043-.146z" fill="#7574B6"></path><path d="M22.043 22.098l-4.63-3.3-.356-.088 4.89 3.483a8.56 8.56 0 0 0 .096-.095z" fill="#C7C6E3"></path><path d="M21.946 22.193a9.945 9.945 0 0 0 2.664-4.437l-.332.182a9.678 9.678 0 0 1-2.456 4.004" fill="#6264AD"></path><g transform="translate(17 18)"><mask id="SNSBNL2A-Y" fill="#fff"><use xlink="#SNSBNL2A-W"></use></mask><path d="M.057.71l.362 5.992a10.045 10.045 0 0 0 4.527-2.509L.056.71z" fill="url(#SNSBNL2A-X)" mask="url(#SNSBNL2A-Y)"></path></g><path d="M21.946 22.193l-4.89-3.483.151.27 4.64 3.307c.034-.03.067-.063.1-.094z" fill="#EF4130"></path><path d="M17.551 24.667l-.344-5.688-.15-.27.362 5.992c.045-.01.088-.022.132-.034z" fill="#EF4130"></path><g transform="translate(17 22)"><mask id="SNSBNL2A-ab" fill="#fff"><use xlink="#SNSBNL2A-Z"></use></mask><path d="M.536 2.395l-.117.306A10.054 10.054 0 0 0 4.946.194l-.32-.066a9.767 9.767 0 0 1-4.09 2.267z" fill="url(#SNSBNL2A-aa)" mask="url(#SNSBNL2A-ab)"></path></g><g transform="translate(12 18)"><mask id="SNSBNL2A-ae" fill="#fff"><use xlink="#SNSBNL2A-ac"></use></mask><path d="M.244 6.611a9.963 9.963 0 0 0 5.175.091L5.057.71" fill="url(#SNSBNL2A-ad)" mask="url(#SNSBNL2A-ae)"></path></g><path d="M17.419 24.702l-.362-5.993-.112.348.343 5.676.131-.031z" fill="#F8ABA6"></path><path d="M12.244 24.61l.142.04 4.558-5.591.113-.35" fill="#FCD3CE"></path><g transform="translate(12 24)"><mask id="SNSBNL2A-ah" fill="#fff"><use xlink="#SNSBNL2A-af"></use></mask><path d="M.245.611a9.962 9.962 0 0 0 5.174.091l-.15-.238A9.67 9.67 0 0 1 .57.422" fill="url(#SNSBNL2A-ag)" mask="url(#SNSBNL2A-ah)"></path></g></g></svg>
                                                                        <p>SNS</p>
                                                                    </li>
                                                                    <li>
                                                                        <svg width="30" height="30" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M22 0H8a8 8 0 0 0-8 8v14a8 8 0 0 0 8 8h14a8 8 0 0 0 8-8V8a8 8 0 0 0-8-8z" fill="#00927B"></path><path fillRule="evenodd" clipRule="evenodd" d="M25 15.394c0-2.83-1.207-5.569-2.81-7.192-1.023-1.02-1.951-1.647-3.323-2.251l-.139.255c.65.396 1.186.813 1.813 1.44 1.787 1.715 2.973 4.686 2.973 7.122 0 .371 0 1.113-.046 1.647-.21-1.044-.861-2.157-1.767-3.063-.255-.255-.626-.65-.905-.789 0 .534-.209 1.764-.464 2.483.348.279.534.487.859.789.743.765 1.23 1.74 1.23 2.552 0 .464-.209.951-.58 1.322-.372.371-1.138.65-1.904.65-.907 0-2.254-.487-2.95-.998 1.416-1.437 2.275-2.993 2.81-4.94a9.333 9.333 0 0 0 .349-2.507c0-1.507-.372-2.947-1.023-4.083C18.078 6.02 16.499 5 14.43 5c-2.46 0-4.76 1.206-6.479 2.947C5.976 9.94 5 12.377 5 14.884c0 .44.023.858.093 1.345h.28a10.7 10.7 0 0 1 .325-2.296c.395-1.555 1.463-3.271 2.81-4.618.976-.974 2.416-1.925 3.414-2.32-.766.627-1.51 1.787-1.88 3.087-.094.348-.186.812-.186 1.136.464-.278 1.625-.695 2.368-.811.162-1.069.559-2.251 1.348-3.04.395-.394.86-.65 1.44-.65.882 0 1.58.65 2.021 1.392.441.765.72 2.135.604 3.063a10.231 10.231 0 0 0-2.834-.418c-.93 0-1.858.14-2.858.418-1.625.44-3.112 1.275-4.18 2.342-1.138 1.138-2.02 2.715-2.02 4.34 0 1.693.859 3.11 1.996 4.269C9.647 24.024 12.363 25 15.035 25c2.16 0 4.088-.744 5.83-2.02l-.139-.231c-1.394.765-2.857 1.136-4.506 1.136-2.485 0-4.855-.765-6.69-2.157a5.579 5.579 0 0 0 1.65.279c.812 0 2.3-.232 3.019-.673-.51-.301-1.44-1.045-1.929-1.625-.696.279-1.555.487-2.322.487-.72 0-1.51-.162-2.021-.695a1.923 1.923 0 0 1-.488-1.253c0-.974.534-1.787 1.185-2.46a5.886 5.886 0 0 1 1.58-1.16c.534 1.972 1.44 3.55 2.857 4.965 1.765 1.741 4.065 2.715 6.085 2.715 1.534.023 2.834-.417 3.925-1.507C24.234 19.617 25 17.529 25 15.394zm-7.665-1.623c-.488 1.507-1.395 2.876-2.486 3.944-1.068-1.138-1.788-2.622-2.16-4.107a8.831 8.831 0 0 1 1.928-.21c.929 0 1.836.14 2.718.373z" fill="#fff"></path></svg>
                                                                        <p>Triodos Bank</p>
                                                                    </li>
                                                                    <li>
                                                                        <svg width="30" height="30" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="30" height="30" rx="8" fill="#8ACE00"></rect><path fillRule="evenodd" clipRule="evenodd" d="M15.894 13.407v.682h.683v-.682h.923v1.235h-4.75v-1.235h.91v.682h.683v-.682m.483-.247v-.018a.236.236 0 0 0-.132-.215c-.154-.085-.988-.644-1.14-1.434-.084-.416-.08-.775.086-.987.051-.065.144-.171.144-.217-.002-.102-.16-.4-.198-.605-.027-.18-.045-.49.105-.658.148-.167.283-.146.232-.309-.038-.12-.122-.318-.122-.6 0-.184.04-.49.258-.556.172-.05.216-.088.229-.12.012-.03 0-.482.032-.684.03-.174.129-.698.603-.75L14.999 6l.079.007c.473.052.574.576.602.75.033.202.019.654.033.685.013.031.056.068.228.119.218.066.26.372.26.555 0 .283-.084.48-.123.601-.051.163.083.142.231.309.15.168.135.477.106.658-.038.204-.196.503-.2.605 0 .046.094.152.145.217.166.212.17.57.088.987-.153.79-.99 1.35-1.142 1.434a.238.238 0 0 0-.132.215v.018M15 15.382h1.987l.263-.493h-4.5l.263.493m1.988 5.185h2.736l.513 3.457h-2.113l-.318-1.494s-.1-.44-.818-.44c-.716 0-.817.44-.817.44l-.32 1.494H11.75l.512-3.457h2.74zm2.499-.247l-.226-.493h-4.55l-.224.493m-2-8.394l.073.004c.476.053.574.61.601.796.037.207.025.685.037.713.012.033.058.075.229.13.214.067.259.388.259.584 0 .296-.082.505-.12.624-.054.176.08.153.225.331.154.175.138.501.107.69-.034.213-.196.532-.2.634 0 .048.093.158.146.224.167.23.175.609.09 1.044-.158.832-.961 1.415-1.12 1.509-.115.067-.104.235-.104.289v3.291h-.447v-3.291c0-.054.013-.222-.104-.29-.157-.093-.96-.676-1.119-1.508-.084-.435-.079-.814.091-1.044.05-.066.145-.176.144-.224-.005-.102-.166-.421-.202-.635-.026-.188-.045-.514.108-.689.146-.178.282-.155.225-.33-.034-.12-.118-.33-.118-.625 0-.196.046-.517.26-.585.172-.054.216-.096.228-.128.012-.03 0-.507.034-.714.027-.186.128-.743.604-.796m9.073-.004l-.075.004c-.474.053-.573.61-.6.796-.038.207-.025.685-.038.713-.01.033-.055.075-.224.13-.217.067-.26.388-.26.584 0 .296.08.505.116.624.057.176-.079.153-.227.331-.15.175-.133.501-.105.69.036.213.196.532.201.634 0 .048-.096.158-.144.224-.168.23-.176.609-.09 1.044.158.832.96 1.415 1.12 1.509.113.067.1.235.1.289v3.291h.45v-3.291c0-.054-.012-.222.102-.29.16-.093.963-.676 1.12-1.508.086-.435.078-.814-.09-1.044-.05-.066-.145-.176-.145-.224.004-.102.166-.421.2-.635.029-.188.048-.514-.106-.689-.147-.178-.281-.155-.225-.33.038-.12.119-.33.119-.625 0-.196-.046-.517-.26-.585-.169-.054-.216-.096-.228-.128-.013-.03 0-.507-.035-.714-.029-.186-.127-.743-.604-.796m-3.052 6.154h-.641V16.86h.641v1.223zm-2.149 0h-.641V16.86h.641v1.223zm2.518-2.455h-3.527L13 19.58h4.25l-.361-3.95z" fill="#fff"></path><path fillRule="evenodd" clipRule="evenodd" d="M15.894 13.407v.682h.683v-.682h.923v1.235h-4.75v-1.235h.91v.682h.683v-.682m.483-.247v-.018a.236.236 0 0 0-.132-.215c-.154-.085-.988-.644-1.14-1.434-.084-.416-.08-.775.086-.987.051-.065.144-.171.144-.217-.002-.102-.16-.4-.198-.605-.027-.18-.045-.49.105-.658.148-.167.283-.146.232-.309-.038-.12-.122-.318-.122-.6 0-.184.04-.49.258-.556.172-.05.216-.088.229-.12.012-.03 0-.482.032-.684.03-.174.129-.698.603-.75L14.999 6l.079.007c.473.052.574.576.602.75.033.202.019.654.033.685.013.031.056.068.228.119.218.066.26.372.26.555 0 .283-.084.48-.123.601-.051.163.083.142.231.309.15.168.135.477.106.658-.038.204-.196.503-.2.605 0 .046.094.152.145.217.166.212.17.57.088.987-.153.79-.99 1.35-1.142 1.434a.238.238 0 0 0-.132.215v.018M15 15.382h1.987l.263-.493h-4.5l.263.493m1.988 5.185h2.736l.513 3.457h-2.113l-.318-1.494s-.1-.44-.818-.44c-.716 0-.817.44-.817.44l-.32 1.494H11.75l.512-3.457h2.74zm2.499-.247l-.226-.493h-4.55l-.224.493m-2-8.394l.073.004c.476.053.574.61.601.796.037.207.025.685.037.713.012.033.058.075.229.13.214.067.259.388.259.584 0 .296-.082.505-.12.624-.054.176.08.153.225.331.154.175.138.501.107.69-.034.213-.196.532-.2.634 0 .048.093.158.146.224.167.23.175.609.09 1.044-.158.832-.961 1.415-1.12 1.509-.115.067-.104.235-.104.289v3.291h-.447v-3.291c0-.054.013-.222-.104-.29-.157-.093-.96-.676-1.119-1.508-.084-.435-.079-.814.091-1.044.05-.066.145-.176.144-.224-.005-.102-.166-.421-.202-.635-.026-.188-.045-.514.108-.689.146-.178.282-.155.225-.33-.034-.12-.118-.33-.118-.625 0-.196.046-.517.26-.585.172-.054.216-.096.228-.128.012-.03 0-.507.034-.714.027-.186.128-.743.604-.796m9.073-.004l-.075.004c-.474.053-.573.61-.6.796-.038.207-.025.685-.038.713-.01.033-.055.075-.224.13-.217.067-.26.388-.26.584 0 .296.08.505.116.624.057.176-.079.153-.227.331-.15.175-.133.501-.105.69.036.213.196.532.201.634 0 .048-.096.158-.144.224-.168.23-.176.609-.09 1.044.158.832.96 1.415 1.12 1.509.113.067.1.235.1.289v3.291h.45v-3.291c0-.054-.012-.222.102-.29.16-.093.963-.676 1.12-1.508.086-.435.078-.814-.09-1.044-.05-.066-.145-.176-.145-.224.004-.102.166-.421.2-.635.029-.188.048-.514-.106-.689-.147-.178-.281-.155-.225-.33.038-.12.119-.33.119-.625 0-.196-.046-.517-.26-.585-.169-.054-.216-.096-.228-.128-.013-.03 0-.507-.035-.714-.029-.186-.127-.743-.604-.796m-3.052 6.154h-.641V16.86h.641v1.223zm-2.149 0h-.641V16.86h.641v1.223zm2.518-2.455h-3.527L13 19.58h4.25l-.361-3.95z" fill="#fff"></path></svg>
                                                                        <p>van Lanschot</p>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div> : null
                                            }
                                        </>
                                    ) : null
                                }
                            </div>
                            <br></br><br></br>
                            <div id="checkout-container"></div>
                        </div>
                    ) : null
                }
                {
                    !showCardModal ? null : (
                        <>
                            <div className="modal fade data-ready in" id="cardModal" role="dialog" style={{ display: 'block' }}>
                                <div className="modal-dialog ">
                                    <div className="modal-content">
                                        <div className="modal-header">
                                            <button type="button" className="close" data-dismiss="modal" onClick={() => closeCardModal()}>&times;</button>
                                            <h4 className="modal-title header-heading">{t('confirm_credit_card_details')}</h4>
                                        </div>
                                        <div className="modal-body">
                                            <div className="row">
                                                <div className="col-sm-6">
                                                    <div className="form-group">
                                                        <label>{t('card_holder_name')}</label>
                                                        <input
                                                            type='text'
                                                            className="form-control"
                                                            value={accountSettings.card_holder_name}
                                                            readOnly
                                                        />
                                                    </div>
                                                </div>
                                                <div className="col-sm-6">
                                                    <label>{t('card_number')}</label>
                                                    <input
                                                        type='text'
                                                        className="form-control"
                                                        value={accountSettings.card_number}
                                                        readOnly
                                                    />
                                                </div>
                                            </div>
                                            <div className="row">
                                                <div className="col-sm-4">
                                                    <div className="form-group">
                                                        <label>{`${'expire'} ${'month'}`}</label>
                                                        <input
                                                            type='text'
                                                            className="form-control"
                                                            value={accountSettings.expire_month}
                                                            readOnly
                                                        />
                                                    </div>
                                                </div>
                                                <div className="col-sm-4">
                                                    <label>{`${'expire'} ${'year'}`}</label>
                                                    <input
                                                        type='text'
                                                        className="form-control"
                                                        value={accountSettings.expire_year}
                                                        readOnly
                                                    />
                                                </div>
                                                <div className="col-sm-4">
                                                    <label>{t('cvc')}#</label>
                                                    <input
                                                        type='password'
                                                        className="form-control"
                                                        value={accountSettings.cvc}
                                                        readOnly
                                                    />
                                                </div>
                                            </div>
                                            <p>{t('payments.want_to_update_credit_card')} <Link to="#" onClick={() => handleClickHere()}>{t('click_here')}</Link></p>
                                        </div>
                                        <div className="modal-footer">
                                            <button type="button" className="btn btn-default" data-dismiss="modal" onClick={() => closeCardModal()}>{t('Cancel')}</button>
                                            <button type="button" className="btn btn-primary" onClick={() => handleConfirmPayment()}>{t('proceed')}</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div className="modal-backdrop fade in"></div>
                        </>
                    )
                }
            </div>
        </>
    )
}

export default withTranslation()(PaymentCheckout)