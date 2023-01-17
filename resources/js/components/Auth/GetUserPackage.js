import axios from 'axios';
import React, { useEffect } from 'react'

function GetUserPackage({ parentCallback }) {

    useEffect(() => {
        const load = () => {
            axios.get('/api/get-user-package?lang=' + localStorage.lang)
                .then((response) => { parentCallback(response.data.data); })
                .catch((error) => { console.log(error); })
        }
        load();
    }, []);

    return (
        <>
            {/* child */}
        </>
    )
}

export default GetUserPackage
