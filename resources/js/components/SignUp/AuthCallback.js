import React, { useEffect, useState } from 'react'
const queryString = require('query-string');
import Swal from 'sweetalert2';
import { withTranslation } from 'react-i18next';
import { Button, Col, Row } from 'react-bootstrap';
import { Link } from 'react-router-dom';
import SiteLogo from '../../assets/images/main-logo.svg';
import Spinner from '../includes/spinner/Spinner';
import SignInImg from '../../assets/images/signin.svg';

function AuthCallback(props) {
    const { t } = props;
    const [loading, setLoading] = useState(false);
    useEffect(() => {
        const parsed = queryString.parse(window.location.search, {});
        let authName = window.location.pathname.split("/").pop();

        let parse_code = '';
        let oauth_verifier = '';
        let callbackURl = '';
        if (authName == 'twitter-callback') {
            parse_code = parsed.oauth_token;
            oauth_verifier = parsed.oauth_verifier;
            callbackURl = '/api/auth/' + authName + '?oauth_token=' + parse_code + '&oauth_verifier=' + oauth_verifier + '&lang=' + localStorage.lang;
        } else {
            parse_code = parsed.code;
            callbackURl = '/api/auth/' + authName + '?code=' + parse_code + '&lang=' + localStorage.lang;
        }

        setLoading(true)
        axios.get(callbackURl)
            .then(async (res) => {
                if (res.data.status) {
                    await axios.get("/api/get-country/" + res.data.data.country_id + '?lang=' + localStorage.lang)
                        .then((response2) => {
                            if (response2.data.country)
                                localStorage["country"] = response2.data.country.code;
                            localStorage["jwt_token"] = res.data.token_type + ' ' + res.data.access_token;
                            localStorage["user_name"] = res.data.data.name;
                            Swal.fire({
                                title: '',
                                text: res.data.message,
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonText: t('OK'),
                            }).then((result) => {
                                if (result.value) {
                                    window.location.href = '/dashboard';
                                }
                            });
                        })
                        .catch((errors2) => {
                            localStorage["jwt_token"] = res.data.token_type + ' ' + res.data.access_token;
                            localStorage["user_name"] = res.data.data.name;
                            Swal.fire({
                                title: '',
                                text: res.data.message,
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonText: t('OK'),
                            }).then((result) => {
                                if (result.value) {
                                    window.location.href = '/dashboard';
                                }
                            });
                        })
                }
                setLoading(false)
            })
            .catch(error => {
                setLoading(false)
            })

    }, []);



    return (
        <>
            <div className="">
                {loading ? <Spinner /> : null}
                <section className="form-wrap">
                    <div className="form-sign forget-form">
                        <Row>
                            <Col lg="6" className='order-1 order-lg-0 mb-lg-0 mb-5'>
                                <div className="form-inner-wrapper">
                                    <div className="form-header d-none d-lg-block">
                                        <strong>
                                            <Link to="/" className="navbar-brand">
                                                <img src={SiteLogo} alt="Logo" className="img-fluid" />
                                            </Link>
                                        </strong>
                                    </div>
                                    <div className="form-account-wrap">
                                        <form className="form-container">
                                            <div className="form-conetnt">
                                                <h3 className="text-uppercase text-black">{t('problem_logging_in')}</h3>
                                                <div className='text-center'> {t('there_was_a_problem_with_the_social_sign_in_please_try_again_in_a_short_while')} </div>

                                                <Link to="/" >
                                                    <Button type="button" className="btn btn-primary mb-5">
                                                        <span className="text-capitalize">{t('Home')}</span>
                                                    </Button>
                                                </Link>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </Col>
                            <Col lg="6" className='order-0 order-lg-1'>
                                <div className="form-header d-lg-none d-block ps-4 pb-4">
                                    <strong>
                                        <Link to="/" className="navbar-brand">
                                            <img src={SiteLogo} alt="Logo" className="img-fluid" />
                                        </Link>
                                    </strong>
                                </div>
                                <div className="img-wrapper">
                                    <img src={SignInImg} alt="" className="img-fluid" />
                                </div>
                            </Col>
                        </Row>
                    </div>
                </section>

            </div>
        </>
    )




}
export default withTranslation()(AuthCallback)