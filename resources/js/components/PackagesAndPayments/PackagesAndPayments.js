import React, { useState } from 'react';
import PrivateRoute from '../helpers/PrivateRoute';

import Billing from './Billing';
import MollieConfirmation from './MollieConfirmation';
import Packages from './Packages';
import PaymentCheckout from './PaymentCheckout';
import Payments from './Payments';
import UpgradePackage from './UpgradePackage';

import { Route, Switch } from "react-router-dom";

function PackagesAndPayments(props) {
    return (
        <>
            <Switch>
                {/* <PrivateRoute path="/packages" exact showSideBar={false} component={Packages} /> */}
                <PrivateRoute path="/packages/billing" exact component={Billing} />
                <PrivateRoute path="/packages/mollie-confirmation" exact component={MollieConfirmation} />
                <PrivateRoute path="/packages/payment-checkout" exact component={PaymentCheckout} />
                <PrivateRoute path="/packages/payments" exact component={Payments} />
                <PrivateRoute path="/packages/upgrade-package" exact component={UpgradePackage} />

            </Switch>
        </>
    );
}

export default PackagesAndPayments;