import React from "react";
import { withTranslation } from 'react-i18next';
// import AuthHeader from "../spinner/Spinner";
import Header from "../../Header/Header";
// import GuestHeader from "../sections/GuestHeader";

const SpinnerBlank = (props) => {
    // let selectedHeader = "";
    let selectedHeader = <Header />;
    const { t } = props;


    // if (localStorage.jwt_token) {
    //     selectedHeader = <AuthHeader />;
    // } else {
    //     selectedHeader = <GuestHeader />;
    // }



    return (
        <>
            {/* {selectedHeader} */}
            <div className="loader-cover">
                <div className="loader-body">
                    <div className="loader"></div>
                    <p>{t('Please wait...')}</p>
                </div>
            </div>
        </>
    );
};
export default withTranslation()(SpinnerBlank)