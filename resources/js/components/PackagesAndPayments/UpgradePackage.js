import React, { useState, useEffect } from 'react'
import Packages from './Packages'

function UpgradePackage() {

    useEffect(() => {
        const load = () => {
            $('html, body').animate({ scrollTop: 0 }, 0);
        }
        load();
    }, [])

    return (
        <div>
            <Packages />
        </div>
    )
}

export default UpgradePackage