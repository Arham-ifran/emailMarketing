import React from "react";
import "./Spinner.css";

export default (props) => {
    return (
        <div id="page-overlay">
            {props.thiscount}
            {/* <div className="page-overlay-loader"></div> */}
            <div className="load-wrapper">
                <span className="load"></span>
            </div>
        </div>
    );
};