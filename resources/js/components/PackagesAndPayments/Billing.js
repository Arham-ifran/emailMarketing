import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import Spinner from "../includes/spinner/Spinner";
import SmallSpinner from "../includes/spinner/SmallSpinner";
import Swal from "sweetalert2";
import * as Constants from "../../constants";
import moment from "moment";
// import "moment-timezone";
import { withTranslation } from "react-i18next";
function Billing(props) {
    const { t } = props;
    const [loading, setLoading] = useState(false);
    const [account_settings, setAccount_settings] = useState({});
    const [package_subscription, setPackage_subscription] = useState({});
    const [package_title, setPackage_title] = useState("");
    const [profile, setProfile] = useState("");
    const [package_recurring_flag, setPackage_recurring_flag] = useState(0);
    const [is_expired, setIs_expired] = useState(false);
    const [card_loading, setCard_loading] = useState(true);
    const [next_payment_date, setNext_payment_date] = useState('')
    const [contacts, setContacts] = useState(0)

    useEffect(() => {
        const load = () => {
            setLoading(true);

            axios
                .get(
                    Constants.BASE_URL +
                    "/api/auth/account-settings?lang=" +
                    localStorage.lang
                )
                .then((response) => {
                    setAccount_settings(response.data.data);

                    if (response.data.data == null) setCard_loading(false);
                });

            axios.get(Constants.BASE_URL + '/api/auth/profile?lang=' + localStorage.lang).then(response => {
                setProfile(response.data.data)
                setContacts(response.data.contacts)
                console.log(Object.keys(response.data.data).length)
            });

            axios
                .get(
                    Constants.BASE_URL +
                    "/api/subscription/check-status?lang=" +
                    localStorage.lang
                )
                .then((response) => {
                    setIs_expired(response.data.data.is_expired);
                });

            axios
                .get(
                    Constants.BASE_URL +
                    "/api/subscription/get-current-package?lang=" +
                    localStorage.lang
                )
                .then((response) => {
                    setPackage_recurring_flag(
                        response.data.data.package_recurring_flag
                    );
                    setPackage_subscription(
                        response.data.data.package_subscription
                    );
                    setPackage_title(response.data.data.package_title);
                    console.log(
                        Object.keys(response.data.data.package_subscription)
                            .length
                    );
                    if (
                        response.data.data.package_subscription &&
                        response.data.data.package_subscription.end_date &&
                        profile != {}
                    ) {
                        setNext_payment_date(
                            moment
                                .unix(
                                    response.data.data.package_subscription
                                        .end_date
                                )
                                .tz(localStorage.timezone)
                                .format("MMM DD, yyyy")
                        );
                        // setNext_payment_date(moment.unix(response.data.data.package_subscription.end_date).format('MMM DD, yyyy'));
                    }
                });
            setLoading(false);
        };

        load();
    }, []);

    const handleOnClickCancelSubscription = () => {
        // event.preventDefault()
        const { t } = props;
        // swal({
        //     title: 'alert_messages.unsubscribe_package_title',
        //     text: 'alert_messages.unsubscribe_package_text',
        //     icon: "warning",
        //     buttons: ['cancel', 'ok'],
        //     dangerMode: true,
        // })
        Swal.fire({
            title: t("Unsubscribing"),
            text: t("Are you sure you want to unsubscribe this package?"),
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: t("Yes, Unsubscribe!"),
            cancelButtonText: t(`Cancel`),
        }).then((result) => {
            if (result.isConfirmed) {
                setLoading(true);
                axios
                    .get(
                        Constants.BASE_URL +
                        "/api/subscription/cancel-current-package?lang=" +
                        localStorage.lang
                    )
                    .then((response) => {
                        setPackage_recurring_flag(0);
                        setLoading(false);
                        Swal.fire({
                            title: t("Success"),
                            text: response.data.message,
                            icon: "success",
                            showCancelButton: false,
                            confirmButtonText: t("OK"),
                        });
                    });
            }
        });
    };

    return (
        <>
            {loading ? <Spinner /> : null}
            <div className="main-content">
                <section className="contact-form-info contact-form-wrap">
                    <div className="container-fluid">
                        <div className="row">
                            <div className="col-md-5">
                                <div className="pricing-packages billing-plan-box billing-box-wrap bill-payment-plan">
                                    <h2>{t("Current Plan")}</h2>
                                    {Object.keys(package_subscription).length >
                                        0 ? (
                                        <>
                                            <div className="generic_content">
                                                <div className="generic_head_price pb-3">
                                                    <div className="generic_head_content">
                                                        <div className="head_bg"></div>
                                                        <div className="head">
                                                            <span>
                                                                {package_title}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div className="generic_feature_list px-2">
                                                    {/* <ul className="current-plan-box">
                                                                {
                                                                    package_subscription.linked_features.map(feature => (
                                                                        <li key={feature.id}>
                                                                            <i className="fa fa-check-circle" aria-hidden="true"></i> {feature.name}
                                                                            {
                                                                                feature.count ? ` (${ feature.count })` : null
                                                                            }
                                                                        </li>
                                                                    ))
                                                                }
                                                            </ul> */}
                                                    <div
                                                        dangerouslySetInnerHTML={{
                                                            __html: package_subscription.description,
                                                        }}
                                                    />
                                                </div>
                                                <br></br>
                                            </div>
                                        </>
                                    ) : (
                                        <SmallSpinner />
                                    )}
                                </div>
                            </div>
                            <div className="col-md-7">
                                <div className="billing-date-box billing-box-wrap">
                                    <h2>{t("Billing")}</h2>
                                    {package_subscription.package_id == 9 ? (
                                        ""
                                    ) : Object.keys(package_subscription)
                                        .length > 0 &&
                                        Object.keys(profile).length > 0 ? (
                                        profile.on_trial == 1 ? (
                                            is_expired == 1 ? (
                                                <p>{t("trial_has_expired")}.</p>
                                            ) : (
                                                <p>
                                                    {t("trial will expire")}:{" "}
                                                    {next_payment_date}{" "}
                                                </p>
                                            )
                                        ) : package_subscription.package_id ==
                                            2 ? (
                                            <p>
                                                {t(
                                                    "Please upgrade your plan to get more features."
                                                )}
                                            </p>
                                        ) : is_expired == 1 ? (
                                            <p>{t("plan_has_expired")}</p>
                                        ) : package_recurring_flag == 1 ? (
                                            <p className="d-flex justify-content-between align-items-center flex-xl-row flex-md-column flex-sm-row flex-column">
                                                {t("next payment due")}:{" "}
                                                {next_payment_date}
                                                &nbsp;&nbsp;
                                                <button
                                                    type="button"
                                                    className="btn btn-primary mt-sm-1 mt-2"
                                                    onClick={() =>
                                                        handleOnClickCancelSubscription()
                                                    }
                                                >
                                                    <span>
                                                        {t(
                                                            "cancel subscription"
                                                        )}
                                                    </span>
                                                </button>
                                            </p>
                                        ) : (
                                            <p>
                                                {" "}
                                                {t("billing_will_expire")}:{" "}
                                                {next_payment_date}
                                            </p>
                                        )
                                    ) : (
                                        <SmallSpinner />
                                    )}
                                </div>
                                <div class="checkout-des mt-md-5 mt-3">
                                    {/* <div class="inner-des">
                                        <div class="d-flex text-w">{t('Total Contacts')}:</div>
                                        <div class="d-flex">{contacts}</div>
                                    </div> */}
                                    {package_subscription.package_id == 9 ? (
                                        <>
                                            {/* <div class="inner-des">
                                                <div class="d-flex text-w">{t('contacts_limit')}:</div>
                                                <div class="d-flex"> {t('unlimited')} </div>
                                            </div> */}
                                            <div className="border border-light my-2"></div>
                                            <div class="inner-des">
                                                <div class="d-flex text-w">{t('Total Emails Sent')}:</div>
                                                <div class="d-flex">{package_subscription.email_used}</div>
                                            </div>
                                            <div class="inner-des">
                                                <div class="d-flex text-w">
                                                    {t("Total SMS Sent")}:
                                                </div>
                                                <div class="d-flex">
                                                    {
                                                        package_subscription.sms_used
                                                    }
                                                </div>
                                            </div>
                                            <div className="border border-light my-2"></div>
                                            <div class="inner-des">
                                                <div class="d-flex text-w">
                                                    {t("New Emails Sent")}:
                                                </div>
                                                <div class="d-flex">
                                                    {
                                                        package_subscription.emails_paying_for
                                                    }
                                                </div>
                                            </div>
                                            <div class="inner-des">
                                                <div class="d-flex text-w">
                                                    {t("Emails Price")}:
                                                </div>
                                                <div class="d-flex">
                                                    {
                                                        package_subscription.emails_to_pay
                                                    }
                                                </div>
                                            </div>
                                            <div className="border border-light my-2"></div>
                                            <div class="inner-des">
                                                <div class="d-flex text-w">
                                                    {t("New SMS Sent")}:
                                                </div>
                                                <div class="d-flex">
                                                    {
                                                        package_subscription.sms_paying_for
                                                    }
                                                </div>
                                            </div>
                                            <div class="inner-des">
                                                <div class="d-flex text-w">
                                                    {t("SMS Price")}:
                                                </div>
                                                <div class="d-flex">
                                                    {
                                                        package_subscription.sms_to_pay
                                                    }
                                                </div>
                                            </div>
                                            {/* <div className="border border-light my-2"></div>
                                            <div class="inner-des">
                                                <div class="d-flex text-w">
                                                    {t("New Contacts Created")}:
                                                </div>
                                                <div class="d-flex">
                                                    {
                                                        package_subscription.contacts_paying_for
                                                    }
                                                </div>
                                            </div>
                                            <div class="inner-des">
                                                <div class="d-flex text-w">
                                                    {t("Contacts Price")}:
                                                </div>
                                                <div class="d-flex">
                                                    {
                                                        package_subscription.contacts_to_pay
                                                    }
                                                </div>
                                            </div> */}
                                        </>
                                    ) : (
                                        <>
                                            {/* <div class="inner-des">
                                                <div class="d-flex text-w">{t('contacts_limit')}:</div>
                                                <div class="d-flex"> {package_subscription.contact_limit} </div>
                                            </div> */}
                                            <div className="border border-light my-2"></div>
                                            <div class="inner-des">
                                                <div class="d-flex text-w">{t('Total Emails available')}:</div>
                                                <div class="d-flex">{package_subscription.email_limit}</div>
                                            </div>
                                            <div class="inner-des">
                                                <div class="d-flex text-w">
                                                    {t("Total Emails Sent")}:
                                                </div>
                                                <div class="d-flex">
                                                    {
                                                        package_subscription.email_used
                                                    }
                                                </div>
                                            </div>
                                            <div class="inner-des">
                                                <div class="d-flex text-w">
                                                    {t("Total SMS available")}:
                                                </div>
                                                <div class="d-flex">
                                                    {" "}
                                                    {
                                                        package_subscription.sms_limit
                                                    }
                                                </div>
                                            </div>
                                            <div class="inner-des">
                                                <div class="d-flex text-w">
                                                    {t("Total SMS Sent")}:
                                                </div>
                                                <div class="d-flex">
                                                    {
                                                        package_subscription.sms_used
                                                    }
                                                </div>
                                            </div>
                                        </>
                                    )}
                                </div>
                                {/* <div className="billing-date-box billing-box-wrap">
                                        <h2>{'billing.card_details'}</h2>fvf
                                        {
                                            account_settings && Object.keys(account_settings).length > 0 ? (
                                                <div className="card-box-des">
                                                    <div className="chip-holder">
                                                        <div className="chip">
                                                            <div className="side left"></div>
                                                            <div className="side right"></div>
                                                            <div className="vertical top"></div>
                                                            <div className="vertical bottom"></div>
                                                        </div>
                                                        <div className="name-des text-uppercase"><h3>{account_settings.card_brand}</h3></div>
                                                    </div>
                                                    <p>
                                                        &nbsp; * * * *&nbsp; * * * *&nbsp; * * * *&nbsp; {account_settings.card_last_four_digits}
                                                    </p>
                                                    <div className="date-name-wrap">
                                                        <span className="text-uppercase">{account_settings.card_holder_name}</span>

                                                        <span>{'billing.valid_thru'} <strong>{account_settings.expire_month}/{account_settings.expire_year}</strong></span>
                                                    </div>
                                                </div>
                                            ) : (
                                                    card_loading ? (
                                                        <SmallSpinner />
                                                    ) : (
                                                            <p>{'billing.no_card_found'} <Link to={Constants.ROUTE_PREFIX + "/settings"}> {'click_here'}</Link></p>
                                                        )
                                                )
                                        }
                                    </div> */}
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </>
    );
}

export default withTranslation()(Billing);
