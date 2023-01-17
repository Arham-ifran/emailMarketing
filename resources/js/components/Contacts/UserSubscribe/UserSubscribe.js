import React, { useEffect } from "react";
import { useParams } from "react-router-dom";
import { withTranslation } from 'react-i18next';
function UserSubscribe(props) {
    const { t } = props;
    const { id } = useParams();
    const getContacts = () => {
        axios
            .post("/api/subscribe-contact/" + id + "?lang=" + localStorage.lang)
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
        <div className="container" style={{ height: "100vh" }}>
            <div className="row h-100 w-100 justify-content-center align-items-center">
                <div className="col-md-8 bg-primary p-5 rounded">
                    <h1> {t('You are subscribed')} </h1>
                    <p>
                        {t('Thank you for Subscribing.')}
                    </p>
                </div>
            </div>
        </div>
    );
}

export default withTranslation()(UserSubscribe);
