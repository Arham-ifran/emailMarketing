import React, { Component, useEffect } from 'react'
import { Redirect, Route } from 'react-router-dom'
import ClientLayout from '../layouts/ClientLayout';

function PublicRoute({ component: Component, name, ...rest }) {
    //console.log(rest);
      return (
        <Route
            {...rest}
            render={props =>
                    
                <ClientLayout>
                    <Component {...props} />
                </ClientLayout>
            }
        />
    );
}

export default PublicRoute;