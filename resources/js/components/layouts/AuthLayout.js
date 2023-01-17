import React from "react";

const AuthLayout = ({ children }) => (
  <div className={"login-layout " + localStorage.lang}>
    {children}
  </div>
);

export default AuthLayout;
