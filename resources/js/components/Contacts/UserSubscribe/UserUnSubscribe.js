import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import { withTranslation } from 'react-i18next';
function UserSubscribe(props) {
    const { t } = props;
    const [errors, setErrors] = useState([]);

    const { id } = useParams();
    const getContacts = () => {
        setErrors([]);
        axios
            .post("/api/unsubscribe-contact/" + id + "?lang=" + localStorage.lang)
            .then((response) => {
                setContacts(response.data);
            })
            .catch((error) => {
                if (
                    error.response &&
                    error.response.hasOwnProperty("data") &&
                    error.response.data.hasOwnProperty("message") &&
                    error.response.data.message
                ) {
                    document.getElementById("errorMessage").innerHTML =
                        error.response.data.message;
                }
            });
    };
    useEffect(() => {
        getContacts();
    }, []);

    return (
        <div className="unsubcribe-section">
            <div className="container">
                <div className="row h-100 w-100 justify-content-center align-items-center">
                    <div className="col-md-6 bg-primary p-5 rounded">
                        <h1> {t('You are Unsubscribed')} </h1>
                        <p>
                            {t('We are sad to see you go.')}
                            {/* You can still subscribe by
                        clicking the subscribe buttion in your email when you're
                        ready to hear from us again! */}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default withTranslation()(UserSubscribe);
