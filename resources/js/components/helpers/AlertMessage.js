import React, { Component } from 'react'

export default class AlertMessage extends Component {
    constructor(props) {
        super(props)
    }

    render() {
        let alertMessage = '';

        if (this.props.status != -1) {
            alertMessage = <div className={`alert ${this.props.status ? 'alert-success' : 'alert-danger'} alert-dismissible fade show`} role="alert">
                {this.props.message}
                <button type="button" className="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        }

        return (
            <>
            <div className="main-content">
                {alertMessage}
                </div>
            </>
        )
    }
}
