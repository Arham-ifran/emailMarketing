import React, { useEffect, useState } from 'react'
import Spinner from '../includes/spinner/Spinner';
import SmallSpinner from '../includes/spinner/SmallSpinner'
import * as Constants from "../../constants";
const queryString = require('query-string');
import Swal from 'sweetalert2';
import { withTranslation } from 'react-i18next';
var countInterval = 15;
var ClearInterval = '';
function MollieConfirmation(props) {

    const { t } = props;
    const [loading, setLoading] = useState(false);
    const [intervalCount, setIntervalCount] = useState(5);

    useEffect(() => {
        const load = () => {

            $('html, body').animate({ scrollTop: 0 }, 0);
            const parsed = queryString.parse(props.location.search, {});
            if (parsed.order_id) {

                ClearInterval = setInterval(() => {
                    if (countInterval) {
                        fetchData(parsed.order_id);
                        countInterval--;
                    }
                    else {
                        clearInterval(ClearInterval);
                        setLoading(false)
                        Swal.fire({
                            title: 'Oops',
                            text: 'Payment Pending...',
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonText: t('OK'),
                        }).then((result) => {
                            window.location.href = "/packages/upgrade-package";
                        })
                    }
                }, 1500);
            }
        }
        load();
    }, [])

    const fetchData = (order_id) => {
        const { t } = props;
        axios.post(Constants.BASE_URL + '/api/mollie/verify-order?lang=' + localStorage.lang, { order_id: order_id }).then(response => {
            setLoading(true)
            if (response.data.status == 1) { // payment successful
                setLoading(false)
                setIntervalCount(0);
                Swal.fire({
                    title: t('Success'),
                    text: response.data.message,
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonText: t('OK'),
                }).then((result) => {
                    window.location.href = "/packages/billing";
                })
                countInterval = 0;
                clearInterval(ClearInterval);
            }
            // else if (response.data.status == 2) // opened
            // {
            // setLoading(false)
            //     clearInterval();
            //     Swal.fire({
            //         title: 'Oops',
            //         text: 'Payment Opened. In progress...',
            //         icon: 'info',
            //         showCancelButton: false,
            //         confirmButtonText: t('OK'),
            //     }).then((result) => {
            //         window.location.href = "/packages/upgrade-package";
            //     })
            // }
            // else if (response.data.status == 3) // pending
            // {
            // setLoading(false)
            //         clearInterval();
            //         Swal.fire({
            //         title: 'Oops',
            //         text: 'Payment Pending...',
            //         icon: 'info',
            //         showCancelButton: false,
            //         confirmButtonText: t('OK'),
            //     }).then((result) => {
            //         window.location.href = "/packages/upgrade-package";
            //     })
            // }
            else if (response.data.status == 4) // in case payment is failed
            {
                setLoading(false)
                setIntervalCount(0);
                Swal.fire({
                    title: 'Oops',
                    text: t('Payment failed. Please try upgrading the package and payment again.'),
                    icon: 'error',
                    showCancelButton: false,
                    confirmButtonText: t('OK'),
                }).then((result) => {
                    window.location.href = "/packages/upgrade-package";
                })
                countInterval = 0;
                clearInterval(ClearInterval);
            }
            else if (response.data.status == 5) // expired
            {
                setLoading(false)
                setIntervalCount(0);
                Swal.fire({
                    title: 'Oops',
                    text: t('Payment Expired. Please try upgrading the package and payment again.'),
                    icon: 'error',
                    showCancelButton: false,
                    confirmButtonText: t('OK'),
                }).then((result) => {
                    window.location.href = "/packages/upgrade-package";
                })
                countInterval = 0;
                clearInterval(ClearInterval);
            }
            else if (response.data.status == 6) // in case payment is cancelled
            {
                setLoading(false)
                setIntervalCount(0);
                Swal.fire({
                    title: 'Oops',
                    text: t("Payment Cancelled"),
                    icon: 'error',
                    showCancelButton: false,
                    confirmButtonText: t('OK'),
                }).then((result) => {
                    window.location.href = "/packages/billing";
                })
                countInterval = 0;
                clearInterval(ClearInterval);
            }
            else if (response.data.status == 7) // refund
            {
                setLoading(false)
                setIntervalCount(0);
                Swal.fire({
                    title: t('Success'),
                    text: t('The amount has been refunded!'),
                    icon: 'info',
                    showCancelButton: false,
                    confirmButtonText: t('OK'),
                }).then((result) => {
                    window.location.href = "/packages/billing";
                })
                countInterval = 0;
                clearInterval(ClearInterval);
            }
            else if (response.data.status == 8) // chargeback
            {
                setLoading(false)
                setIntervalCount(0);
                Swal.fire({
                    title: 'Oops',
                    text: t("The amount was charged back"),
                    icon: 'info',
                    showCancelButton: false,
                    confirmButtonText: t('OK'),
                }).then((result) => {
                    window.location.href = "/packages/billing";
                })
                countInterval = 0;
                clearInterval(ClearInterval);
            }
        });
        // setIntervalCount(countInterval - 1)
        if (countInterval < 1) {
            setLoading(false)
            setIntervalCount(0);
            Swal.fire({
                title: t('Success'),
                text: t('Processing Payment') + '\n Your package will be changed as soon as we get a response from mollie',
                icon: 'success',
                showCancelButton: false,
                confirmButtonText: t('OK'),
            }).then((result) => {
                window.location.href = "/packages/billing";
            })
            countInterval = 0;
            clearInterval(ClearInterval);
        }
    }

    return (
        <>
            {loading ? <Spinner /> : null}
            <div className="main-content">
                {loading ? <Spinner /> : null}
                <br></br>
                <br></br>
                <center><h2>{t('Please wait...')}...</h2></center>
                <br></br>
                <center><h2>{t('Processing your request')}</h2></center>
                <br></br>
            </div>
        </>
    )
}

export default withTranslation()(MollieConfirmation)
