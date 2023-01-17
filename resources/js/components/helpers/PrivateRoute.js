import React, { Component, useEffect } from 'react'
import { Redirect, Route } from 'react-router-dom'
import PrivateLayout from "../layouts/PrivateLayout";

function PrivateRoute({ component: Component, name, ...rest }) {
    return (
        <Route
            {...rest}
            render={props =>

                localStorage.jwt_token ? (

                    // <PrivateLayout showSideBar={rest.showSideBar}>
                    <PrivateLayout showSideBar={rest.showSideBar}>
                        <Component {...props} />
                    </PrivateLayout>
                ) : (
                    <Redirect to="/signin" />
                )
            }
        />
    );
}

export default PrivateRoute;