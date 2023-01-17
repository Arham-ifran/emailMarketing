import React from 'react';
import ReactDOM from 'react-dom';
import NotFoundPic from '../assets/images/notfound-text.png';
import { withTranslation } from 'react-i18next';
import { Link } from 'react-router-dom';
function NotFound(props) {
    const { t } = props;
    return (
        <div className="notfound-sec">
            <div className="container">
                <div className="row justify-content-center align-items-center" style={{ height: '100vh' }}>
                    <div className="col-md-8">
                        <div className="text-center img-wrapper">
                            <img src={NotFoundPic} alt="404" className="img-fluid" />
                        </div>
                        <div className="content-wrapper d-flex flex-column align-items-center">
                            <h2 className="text-uppercase">{t('oops!')}</h2>
                            <h6 className="text-uppercase">{t('not found')}</h6>
                            <Link to="/" className="btn btn-primary"> <span>{t('Back to Home')} </span> </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default withTranslation()(NotFound);
