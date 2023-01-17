import React, { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import Spinner from '../includes/spinner/Spinner';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faDownload } from '@fortawesome/free-solid-svg-icons'
import { faRedo } from '@fortawesome/free-solid-svg-icons'
import { faMoneyCheck } from '@fortawesome/free-solid-svg-icons'
import 'datatables.net';
import "../../assets/datatables/dataTables.bootstrap.min"
import * as Constants from "../../constants";
import { withTranslation } from 'react-i18next';

function Payments(props) {
    const { t } = props;
    const [loading, setLoading] = useState(false);
    const [payments, setPayments] = useState([]);
    const [paymentsPAYG, setPaymentsPAYG] = useState([]);
    const [links, setLinks] = useState({});
    const [linksPAYG, setLinksPAYG] = useState({});
    const [meta, setMeta] = useState({});
    const [metaPAYG, setMetaPAYG] = useState({});
    const [payAsYouGo, setPayAsYouGo] = useState(false);

    const [state, setState] = useState({ item_filter: "", amount_filter: "" });

    useEffect(() => {
        const load = () => {

            axios.get(Constants.BASE_URL + '/api/subscription/get-current-package?lang=' + localStorage.lang).then(response => {
                if (response.data.data.package_subscription.package_id == 9) {
                    setPayAsYouGo(true);
                } else
                    setPayAsYouGo(false);
                payAsYouGoPaymentsListing(Constants.BASE_URL + `/api/subscription/pay-as-you-go-payments`);
                paymentsListing(Constants.BASE_URL + `/api/subscription/payments`);
            }
            );
        }
        load();
    }, [])

    const paymentsListing = (url = Constants.BASE_URL + '/api/subscription/payments?lang=' + localStorage.lang) => {
        $('html, body').animate({ scrollTop: 0 }, 0);
        const { t } = props;
        if (url) {

            setLoading(true)

            axios.get(url).then(response => {
                setPayments(response.data.data)
                setLinks(response.data.links)
                setMeta(response.data.meta)
                setLoading(false)
            })
        }
    }

    const payAsYouGoPaymentsListing = (url = Constants.BASE_URL + '/api/subscription/pay-as-you-go-payments?lang=' + localStorage.lang) => {
        $('html, body').animate({ scrollTop: 0 }, 0);
        const { t } = props;
        if (url) {
            setLoading(true)

            axios.get(url).then(response => {
                setPaymentsPAYG(response.data.data)
                setLinksPAYG(response.data.links)
                setMetaPAYG(response.data.meta)
                setLoading(false)
            })
        }
    }

    const handleFieldChange = (event) => {
        const { name, value } = event.target;
        setState(prevState => ({ ...prevState, [name]: value }));
    }

    const handleSearch = () => {
        paymentsListing(Constants.BASE_URL + `/api/subscription/payments?item=${state.item_filter}&amount=${state.amount_filter}`)
    };

    // const { t } = props;
    // let queryParams = `item=${state.item_filter}&amount=${state.amount_filter}`;
    const getQueryParams = () => {
        return `item=${state.item_filter}&amount=${state.amount_filter}`;
    }
    let recordNumber1 = meta.from - 1;
    const paymentsList = payments.map((payment, index) => {
        recordNumber1 = recordNumber1 + 1;
        return (
            <tr key={payment.id}>
                <td>{recordNumber1}</td>
                <td>{payment.item}</td>
                <td><sup>€</sup>{payment.amount}</td>
                <td>{payment.vat_percentage}</td>
                <td><sup>€</sup>{payment.vat_amount}</td>
                <td>{payment.voucher ? payment.reseller : ''}</td>
                <td>{payment.voucher ? payment.voucher : ''}</td>
                <td>{payment.discount_percentage}</td>
                <td><sup>€</sup>{payment.discount_amount}</td>
                <td><sup>€</sup>{payment.total_amount}</td>
                <td>{payment.payment_method}</td>
                <td>{payment.payment_date}</td>
                {/* 1=paid,2=open,3=pending,4=failed,5=expired,6=cancel,7=refund,8=chargeback */}
                <td>{payment.status == 1 ? t('Paid') : (payment.status == 4 ? t('Failed') : (payment.status == 5 ? t('expired') : (payment.status == 6 ? t('Cancelled') : (payment.status == 7 ? t('Refunded') : (payment.status == 8 ? t('Chargeback') : t('In-process'))))))}</td>
                <td>
                    {payment.status == 1 ? <a href={`${Constants.BASE_URL}/api/subscription/download-payment-invoice/${payment.hash_id}?lang=${localStorage.lang}`}><button title={t('save_invoice')} className="dowload-bt btn btn-primary pull-right"><span> <FontAwesomeIcon icon={faDownload} /></span></button></a> : ""}
                </td>
            </tr>
        );
    });

    const regeneratePayment = (id) => {
        setLoading(true)
        axios.get(`${Constants.BASE_URL}/api/regenerate-payment/${id}?lang=${localStorage.lang}`)
            .then(response => {
                setLoading(false)
                if (response.data.status) {
                    location.reload();
                }
            })
    }

    let recordNumber = metaPAYG.from - 1;
    const payAsYouGoPaymentsList = paymentsPAYG.map((paymentAsYouGo, index) => {
        recordNumber = recordNumber + 1;
        return (
            <tr key={paymentAsYouGo.id}>
                <td>{recordNumber}</td>
                <td>{t('payg')}</td>

                <td>{paymentAsYouGo.charging_for_emails}</td>
                <td>{paymentAsYouGo.price_for_emails_charged}</td>
                <td>{paymentAsYouGo.charging_for_sms}</td>
                <td>{paymentAsYouGo.price_for_sms_charged}</td>
                {/* <td>{paymentAsYouGo.charging_for_contacts}</td>
                <td>{paymentAsYouGo.price_for_contacts_charged}</td> */}

                <td><sup>€</sup>{paymentAsYouGo.amount}</td>
                <td>{paymentAsYouGo.vat_percentage}</td>
                <td><sup>€</sup>{paymentAsYouGo.vat_amount}</td>
                {/* <td>{paymentAsYouGo.voucher ? paymentAsYouGo.reseller : 'not_applicable'}</td>
                <td>{paymentAsYouGo.voucher ? paymentAsYouGo.voucher : 'not_applicable'}</td>*/}
                <td>{paymentAsYouGo.discount_percentage}</td>
                <td><sup>€</sup>{paymentAsYouGo.discount_amount}</td>
                <td><sup>€</sup>{paymentAsYouGo.total_amount.toFixed(2)}</td>
                <td>{paymentAsYouGo.payment_method}</td>
                <td>{paymentAsYouGo.payment_date}</td>
                {/* 1=paid,2=open,3=pending,4=failed,5=expired,6=cancel,7=refund,8=chargeback */}
                <td>{paymentAsYouGo.status == 1 ? t('Paid') : (paymentAsYouGo.status == 4 ? t('Failed') : (paymentAsYouGo.status == 5 ? t('Expired') : (paymentAsYouGo.status == 6 ? t('Cancelled') : (paymentAsYouGo.status == 7 ? t('Refunded') : (paymentAsYouGo.status == 8 ? t('Chargeback') : t('In-process'))))))}</td>
                <td>
                    {paymentAsYouGo.status == 1 ?
                        <a href={`${Constants.BASE_URL}/api/subscription/download-pay-as-you-go-invoice/${paymentAsYouGo.hash_id}?lang=${localStorage.lang}`}><button title={t('save_invoice')} className="dowload-bt btn btn-primary pull-right"><span> <FontAwesomeIcon icon={faDownload} /></span></button></a>
                        : ""}
                    {(paymentAsYouGo.status == 4 || paymentAsYouGo.status == 5 || paymentAsYouGo.status == 6) ?
                        <button title={t('regenerate')} className="dowload-bt btn btn-primary pull-right" onClick={() => regeneratePayment(paymentAsYouGo.hash_id)}><span> <FontAwesomeIcon icon={faRedo} /></span></button>
                        : ""}
                    {(paymentAsYouGo.status == 3 || paymentAsYouGo.status == 2) ?
                        <a href={paymentAsYouGo.link}><button title={t('pay now')} className="dowload-bt btn btn-primary pull-right"><span> <FontAwesomeIcon icon={faMoneyCheck} /></span></button></a>
                        :
                        ""}
                </td>
            </tr >
        );
    });

    return (
        <>
            {loading ? <Spinner /> : null}
            <div className="main-content">
                <section className="payas-yougo-tabs cloud-section report">
                    <div className="up-packages pricing-wrapper">
                        <div className="pricing-toggle mt-sm-0 mt-2">
                            <label className="custom-radio price-toggle">
                                <input type="radio" name="radio" checked={payAsYouGo ? 0 : 1} onClick={() => { setPayAsYouGo(false); paymentsListing(Constants.BASE_URL + `/api/subscription/payments` + `?${getQueryParams()}`) }} />
                                <p>{t('package_payments')}</p>
                                <span className="checkmark"></span>
                            </label>
                            <label className="custom-radio price-toggle mt-sm-0 mt-2">
                                <input type="radio" name="radio" checked={payAsYouGo ? 1 : 0} onClick={() => { setPayAsYouGo(true); payAsYouGoPaymentsListing(Constants.BASE_URL + `/api/subscription/pay-as-you-go-payments`) }} />
                                <p>{t('pay_as_you_go_payments')}</p>
                                <span className="checkmark"></span>
                            </label>
                        </div>
                    </div>
                    <div className="container-fluid">
                        {payAsYouGo ?
                            ""
                            :
                            <div className="row">
                                <div className="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-md-0 mb-3">
                                    <div className='form-group'>
                                        <input
                                            type='text'
                                            className='form-control'
                                            placeholder={t('Package Name')}
                                            name='item_filter'
                                            value={state.item_filter}
                                            onChange={(e) => handleFieldChange(e)}
                                        />
                                    </div>
                                </div>
                                <div className="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-md-0 mb-3">
                                    <div className='form-group'>
                                        <input
                                            type='number'
                                            className='form-control'
                                            placeholder={t("Amount")}
                                            name='amount_filter'
                                            value={state.amount_filter}
                                            onChange={(e) => handleFieldChange(e)}
                                        />
                                    </div>
                                </div>
                                <div className="col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-md-0 mb-3">
                                    {/* <div className="search-input-holder payment-search">
                                    <input type="text" name="daterange" className="form-control input-sm input-search-des" />
                                    <button className="btn btn-success" onClick={() => handleSearch()}><i className="fa fa-search" aria-hidden="true"></i></button>
                                </div> */}
                                    <button className="btn btn-primary pull-right" onClick={() => handleSearch()}>
                                        <span>{t('Search')}</span>
                                    </button>
                                </div>
                            </div>
                        }
                        <div className="row mt-md-5 mt-3">
                            <div className="col-md-12">
                                <div className="table-responsive">
                                    {payAsYouGo ?
                                        <table id="payments-datatable-payAsYouGo" className="payment-tb table em-table no-footer dataTable">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{t('Package')}</th>
                                                    <th>{t('Charging for emails')}</th>
                                                    <th>{t('Price for emails charged')}</th>
                                                    <th>{t('Charging for sms')}</th>
                                                    <th>{t('Price for sms charged')}</th>
                                                    {/* <th>{t('Charging for contacts')}</th>
                                                    <th>{t('Price for contacts charged')}</th> */}
                                                    <th>{t('Amount')}</th>
                                                    <th>{t('VAT')} %</th>
                                                    <th>{t('VAT Amount')}</th>
                                                    <th>{t('discount')} %</th>
                                                    <th>{t('discount_amount')}</th>
                                                    <th>{t('Paid Amount')}</th>
                                                    <th>{t('payment_source')}</th>
                                                    <th>{t('Payment Date')}</th>
                                                    <th>{t('Status')}</th>
                                                    <th>{t('Action')}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {paymentsPAYG.length ?
                                                    payAsYouGoPaymentsList
                                                    :
                                                    <tr>
                                                        {/* <td className="text-center">
                                                            {t('no_payments_found')}
                                                        </td> */}
                                                        <td className="text-center" colSpan="8">
                                                            {t('no_payments_found')}
                                                        </td>
                                                        <td className="text-center" colSpan="8">
                                                            {t('no_payments_found')}
                                                        </td>
                                                    </tr>
                                                }
                                            </tbody>
                                        </table>
                                        :
                                        <table id="payments-datatable" className="payment-tb table em-table no-footer dataTable">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{t('Package')}</th>
                                                    <th>{t('Amount')}</th>
                                                    <th>{t('VAT')} %</th>
                                                    <th>{t('VAT Amount')}</th>
                                                    <th>{t('reseller')}</th>
                                                    <th>{t('voucher')}</th>
                                                    <th>{t('discount')} %</th>
                                                    <th>{t('discount_amount')}</th>
                                                    <th>{t('Paid Amount')}</th>
                                                    <th>{t('payment_source')}</th>
                                                    <th>{t('Payment Date')}</th>
                                                    <th>{t('Status')}</th>
                                                    <th>{t('Action')}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {payments.length ?
                                                    paymentsList
                                                    :
                                                    <tr>
                                                        <td className="text-center" colSpan="14">
                                                            {t('no_payments_found')}
                                                        </td>
                                                    </tr>
                                                }
                                            </tbody>
                                        </table>
                                    }
                                </div>
                                {
                                    loading ? null : (
                                        payAsYouGo ?
                                            <p>{t('showing from :') + (metaPAYG.from ? metaPAYG.from : 0) + t(' to ') + (metaPAYG.to ? metaPAYG.to : 0) + t(' of ') + metaPAYG.total + t(' entries')}</p>
                                            :
                                            <p>{t('showing from :') + (meta.from ? meta.from : 0) + t(' to ') + (meta.to ? meta.to : 0) + t(' of ') + meta.total + t(' entries')}</p>

                                    )
                                }
                            </div>
                        </div>
                        {payAsYouGo ?
                            (linksPAYG.prev || linksPAYG.next) ? (
                                <div className="row mt-3">
                                    <div className="col-md-12 text-right desktop-view-pg">
                                        <ul className="pagination pagination-lg">
                                            <li className={linksPAYG.prev ? '' : 'disabled'}>
                                                <Link to="#" onClick={() => { if (linksPAYG.prev) payAsYouGoPaymentsListing(linksPAYG.first) }}>{t('first')}</Link>
                                            </li>
                                            <li className={linksPAYG.prev ? '' : 'disabled'}>
                                                <Link to="#" onClick={() => { if (linksPAYG.prev) payAsYouGoPaymentsListing(linksPAYG.prev) }}>{t('previous')}</Link>
                                            </li>
                                            <li className={linksPAYG.next ? '' : 'disabled'}>
                                                <Link to="#" onClick={() => { if (linksPAYG.next) payAsYouGoPaymentsListing(linksPAYG.next) }}>{t('next')}</Link>
                                            </li>
                                            <li className={linksPAYG.next ? '' : 'disabled'}>
                                                <Link to="#" onClick={() => { if (linksPAYG.next) payAsYouGoPaymentsListing(linksPAYG.last) }}>{t('last')}</Link>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            ) : null
                            :
                            (links.prev || links.next) ? (
                                <div className="row mt-3">
                                    <div className="col-md-12 text-right desktop-view-pg">
                                        <ul className="pagination pagination-lg">
                                            <li className={links.prev ? '' : 'disabled'}>
                                                <Link to="#" onClick={() => { if (links.prev) paymentsListing(links.first + `&${getQueryParams()}`) }}>{t('first')}</Link>
                                            </li>
                                            <li className={links.prev ? '' : 'disabled'}>
                                                <Link to="#" onClick={() => { if (links.prev) paymentsListing(links.prev + `&${getQueryParams()}`) }}>{t('previous')}</Link>
                                            </li>
                                            <li className={links.next ? '' : 'disabled'}>
                                                <Link to="#" onClick={() => { if (links.next) paymentsListing(links.next + `&${getQueryParams()}`) }}>{t('next')}</Link>
                                            </li>
                                            <li className={links.next ? '' : 'disabled'}>
                                                <Link to="#" onClick={() => { if (links.next) paymentsListing(links.last + `&${getQueryParams()}`) }}>{t('last')}</Link>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            ) : null
                        }
                    </div>
                </section>
            </div >
        </>
    )
}

export default withTranslation()(Payments)