import React, { useEffect, useState, useRef } from 'react';
import Sender from "../../assets/images/Image4.png";
import IconFeature from "../../assets/images/Icon feather-download.svg";
import Union5 from "../../assets/images/Union 5.svg";
import Group49 from "../../assets/images/Group 49.svg";
import { LineChart, PieChart, Pie, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer, Cell } from 'recharts';

import Spinner from "../includes/spinner/Spinner";
import Moment from "react-moment";
import moment from 'moment-timezone';
import { CSVLink, CSVDownload } from "react-csv";
import html2canvas from 'html2canvas';
import Pagination from "react-js-pagination";
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faSpinner } from '@fortawesome/free-solid-svg-icons'
import { withTranslation } from 'react-i18next';
const ReportHistory = (props) => {
    const { t } = props;
    const [showSearchBar, setShowSearchBar] = useState(0);

    const [loading, setLoading] = useState(false);
    const [errors, setErrors] = useState([]);
    const [refresh, setRefresh] = useState(0);

    const [term, setTerm] = useState("");
    const [campaign, setCampaign] = useState([]);
    const [reports, setReports] = useState([]);
    const [openLogs, setOpenLogs] = useState([]);
    const [showingContactDetailsFor, setShowingContactDetailsFor] = useState([]);
    const [sent_to, setSent_to] = useState([]);
    const [successes, setSuccesses] = useState([]);
    const [fails, setFails] = useState([]);
    // tab1 data
    const [report, setReport] = useState([]);
    const [totalSent, setTotalSent] = useState([]);
    const [totalSuccess, setTotalSuccess] = useState([]);
    const [totalFails, setTotalFails] = useState([]);

    // for dynamic sections 
    const [showContactOpenDetails, setShowContactOpenDetails] = useState(0);

    const getReport = (campId) => {
        setLoading(true);
        axios
            .get("/api/get-sms-campaign-report/" + campId + "/" + props.history + "?page=" + pageNumber + "&module=" + module + "&search=" + term + "&csv=0&lang=" + localStorage.lang)
            .then((response) => {
                setLoading(false);
                const received_data = response.data;
                setCampaign(received_data.campaign);
                setReports(received_data.reports);
                setSent_to(received_data.sent_to);
                setTotalItems1(received_data.totalSent)
                setSuccesses(received_data.success);
                setFails(received_data.fail);
                setTotalItems2(received_data.totalSuccesses)
                setTotalItems3(received_data.totalFails)
                // tab1 data
                setReport(received_data.report);
                setTotalSent(received_data.totalSent);
                setTotalSuccess(received_data.totalSuccesses);
                setTotalFails(received_data.totalFails);

            })
            .catch((error) => {
                if (error.response.data.errors) {
                    setErrors(error.response.data.errors);
                }
                setLoading(false);
            });
    };
    useEffect(() => {
        // console.log(history);
        let parseUriSegment = window.location.pathname.split("/");
        if (
            parseUriSegment.indexOf("sms-campaign") &&
            parseUriSegment.indexOf("report") != -1
        ) {
            getReport(parseUriSegment[2]);
        }
    }, [refresh]);

    const getReportCSV = () => {
        setLoading(true);
        let parseUriSegment = window.location.pathname.split("/");
        if (
            parseUriSegment.indexOf("sms-campaign") &&
            parseUriSegment.indexOf("report") != -1
        ) {
            var campId = parseUriSegment[2];
            axios
                .get("/api/get-sms-campaign-report/" + campId + "/" + props.history + "?page=" + pageNumber + "&module=" + module + "&search=" + term + "&csv=1&lang=" + localStorage.lang, { responseType: 'blob' })
                .then((response) => {
                    setLoading(false);
                    var fileName = "report-contacts.xlsx"
                    var a = document.createElement("a");
                    var json = response.data,
                        blob = new Blob([json], { type: "octet/stream" }),
                        url = window.URL.createObjectURL(blob);
                    a.href = url;
                    a.download = fileName;
                    a.click();
                    window.URL.revokeObjectURL(url);
                })
                .catch((error) => {
                    if (error.response.data.errors) {
                        setErrors(error.response.data.errors);
                    }
                    setLoading(false);
                });
        }
    };

    // for pagination
    const [pageRange, setPageRange] = useState(5);
    const [perPage, setperPage] = useState(10);
    const [pageNumber, setPageNumber] = useState(1);
    const [module, setModule] = useState(0);
    // // sent_to
    const [pageNumber1, setPageNumber1] = useState(1);
    const [totalItems1, setTotalItems1] = useState(0);
    // // success
    const [pageNumber2, setPageNumber2] = useState(1);
    const [totalItems2, setTotalItems2] = useState(0);
    // // fail
    const [pageNumber3, setPageNumber3] = useState(1);
    const [totalItems3, setTotalItems3] = useState(0);

    const data01 = [
        {
            name: "Delivered",
            value: totalSuccess,
        },
        {
            name: "Failed",
            value: totalFails,
        },
    ];

    const reachColors = ["#24e096", "#FD6A21", "#FFBB28"];

    const DownloadReport = () => {
        setLoading(true);
        document.getElementById('hideInImage').style.display = "none";
        html2canvas(document.getElementById('nav-home')).then(canvas => {
            const image = canvas.toDataURL("image/png");
            var link = document.createElement('a');
            link.download = 'campaign_report.png';
            link.href = canvas.toDataURL();
            link.click();
        })
        document.getElementById('hideInImage').style.display = "block";
        setLoading(false);
    }

    return (
        <>
            {loading && (module == 0 || module == 1 || module == 7 || module == 8) ? <Spinner /> : null}
            <section className="reports">
                <h2>
                    {t('Campaign Name')}: <small> {campaign.name} </small>{" "}
                </h2>
                <div className="about-reports">
                    <nav>
                        <div
                            className="nav nav-tabs"
                            id="nav-tab"
                            role="tablist"
                        >
                            <button
                                className="nav-link active"
                                id="nav-home-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#nav-home"
                                type="button"
                                role="tab"
                                aria-controls="nav-home"
                                aria-selected="true"
                                onClick={() => { setModule(0); setTerm(""); setRefresh(!refresh) }}
                            >
                                {t('Report Summary')}
                            </button>
                            <button
                                className="nav-link"
                                id="nav-profile-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#nav-profile"
                                type="button"
                                role="tab"
                                aria-controls="nav-profile"
                                aria-selected="false"
                                onClick={() => { setModule(1); setTerm(""); setRefresh(!refresh); document.getElementById('home-tab').click(); }}
                            >
                                {t('Recipient Activities')}
                            </button>
                        </div>
                    </nav>
                    <div className="tab-content" id="nav-tabContent">
                        <div className="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" >
                            <div className="icons-container" id='hideInImage'>
                                <div className="icons-content d-flex justify-content-end mb-2">
                                    <div className="icons-content-left d-flex">
                                        <div className="IconFeature icon">
                                            <div onClick={() => DownloadReport()} ><img src={IconFeature} className="img-fluid" alt="" /></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="row abt-contan">
                                <div className="col-md-7 abt-rt partition">
                                    <div className="real-time -space -breaker mb-4">
                                        <h5>{t('Campaign Data')}</h5>
                                    </div>
                                    <div className="rt-data">
                                        <h6>{totalSent}</h6>
                                        <p>{t('Total SMS Sent')}</p>
                                        <span>
                                            <Moment format="DD MMMM YYYY hh:mm:ss">
                                                {moment.tz(moment(_.isEmpty(report) ? campaign.created_at : report.created_at).utc(), localStorage.timezone)}
                                            </Moment>
                                        </span>
                                    </div>
                                    <div className="rt-progress">
                                        <div class="progress">
                                            <div class="progress-bar bg-success" role="progressbar" style={{ width: totalSent == 0 ? 0 : ((totalSuccess / totalSent) * 100).toFixed(2) + "%", ariaValuemax: "100" }}></div>
                                        </div>
                                        <span>
                                            {totalSent == 0 ? 0 : ((totalSuccess / totalSent) * 100).toFixed(2)}
                                            % {t('Delivered')}
                                        </span>
                                    </div>
                                    <div className="rt-dlvr">
                                        <div className="dlvr">
                                            <div className="delivered"></div>
                                            <div className="rt-desc">
                                                <p>
                                                    {t('Delivered')}{" "}
                                                    {totalSent == 0 ? 0 : ((totalSuccess / totalSent) * 100).toFixed(2)}
                                                    %
                                                </p>
                                                <p>
                                                    {totalSuccess}{" "}
                                                    {t('Contacts')}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="dlvr">
                                            <div className="unsent"></div>
                                            <div className="rt-desc">
                                                <p>
                                                    Failed{" "}
                                                    {totalSent == 0 ? 0 : ((totalFails / totalSent) * 100).toFixed(2)}
                                                    %
                                                </p>
                                                <p>
                                                    {totalFails}{" "}
                                                    {t('Contacts')}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-md-5 abt-rt">
                                    <div className="real-time -space -breaker mb-4">
                                        <h5>{t('Campaign Reach')}</h5>
                                    </div>
                                    <div className="reach-image">
                                        <ResponsiveContainer
                                            width="100%"
                                            height={180}
                                        >
                                            <PieChart width={400} height={400}>
                                                <Pie
                                                    data={data01}
                                                    dataKey="value"
                                                    cx="50%"
                                                    cy="50%"
                                                    innerRadius={70}
                                                    outerRadius={90}
                                                >
                                                    {data01.map(
                                                        (entry, index) => (
                                                            <Cell
                                                                key={`cell-${index}`}
                                                                fill={
                                                                    reachColors[
                                                                    index %
                                                                    reachColors.length
                                                                    ]
                                                                }
                                                            />
                                                        )
                                                    )}
                                                </Pie>
                                                <Tooltip />
                                            </PieChart>
                                        </ResponsiveContainer>
                                        <div className="reach-desc">
                                            <p>
                                                {totalSent}
                                            </p>
                                            <p>{t('Total Reach')}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="row d-flex flex-column p-outerbox reach-graph">
                                <div className="d-felx -space -breaker">
                                    <h5>{t('Massage and Sender details')}</h5>
                                </div>
                                <div className="d-flex sender-details -space">
                                    <div className="d-flex about-sender flex-sm-row flex-column">
                                        {/* <div className="d-flex me-lg-5 me-2 justify-content-sm-start">
                                            <div className="sender-image">
                                                <img
                                                    className="img-fluid"
                                                    src={Sender}
                                                />
                                            </div>
                                        </div> */}
                                        <div className="d-flex flex-column w-100 mt-sm-0 mt-3">
                                            <div className="d-flex mb-2  justify-content-sm-start">
                                                <div className="d-flex fixed-w">
                                                    <p>{t('Sender Name')}:</p>
                                                </div>
                                                <div className="d-flex">
                                                    <p>{campaign.sender_name}</p>
                                                </div>
                                            </div>
                                            {campaign.sender_number ?
                                                <div className="d-flex mb-2  justify-content-sm-start">
                                                    <div className="d-flex fixed-w">
                                                        <p>{t('reply_to_number')}:</p>
                                                    </div>
                                                    <div className="d-flex">
                                                        <p> {campaign.sender_number}</p>
                                                    </div>
                                                </div>
                                                : null
                                            }
                                            <div className="d-flex mb-2  justify-content-sm-start">
                                                <div className="d-flex fixed-w">
                                                    <p>{t('Created on')}:</p>
                                                </div>
                                                <div className="d-flex">
                                                    <p>
                                                        <Moment format="DD MMMM YYYY hh:mm:ss">
                                                            {moment.tz(moment(_.isEmpty(report) ? campaign.created_at : report.created_at).utc(), localStorage.timezone)}
                                                        </Moment>
                                                    </p>
                                                </div>
                                            </div>
                                            <div className="d-flex mb-2  justify-content-sm-start">
                                                <div className="d-flex fixed-w">
                                                    <p>{t('Delivered on')}:</p>
                                                </div>
                                                <div className="d-flex">
                                                    <p>
                                                        <Moment format="DD MMMM YYYY hh:mm:ss">
                                                            {moment.tz(_.isEmpty(report) ? campaign.updated_at : report.updated_at)}
                                                        </Moment>
                                                    </p>
                                                </div>
                                            </div>
                                            <div className="d-flex mb-2  justify-content-sm-start">
                                                <div className="d-flex fixed-w">
                                                    <p>{t('Message')}:</p>
                                                </div>
                                                <div className="d-flex">
                                                    <p>{campaign.message}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" >
                            <div className="recipient-activities-tabs">
                                <div className="tabs-container">
                                    <ul class="nav nav-tabs justify-content-sm-between justify-content-around" id="myTab" role="tablist" onClick={() => { setShowContactOpenDetails(0); setShowingContactDetailsFor([]); }}>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#Sent" type="button" role="tab" aria-controls="Sent" aria-selected="true" onClick={() => { setModule(1); setTerm(""); setRefresh(!refresh) }}>{totalSent}<p>{t('Sent')}</p></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#Delivered" type="button" role="tab" aria-controls="Delivered" aria-selected="false" onClick={() => { setModule(2); setTerm(""); setRefresh(!refresh) }}>{totalSuccess}<p>{t('Delivered')}</p></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#Failed" type="button" role="tab" aria-controls="Failed" aria-selected="false" onClick={() => { setModule(3); setTerm(""); setRefresh(!refresh) }}>{totalFails}<p>{t('Failed')}</p></button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tab-content" id="myTabContent">
                                    {loading && (module == 1 || module == 2 || module == 3 || module == 4 || module == 5 || module == 6) ? <FontAwesomeIcon icon={faSpinner} spin /> : null}

                                    <div class="tab-pane fade show active" id="Sent" role="tabpanel" aria-labelledby="home-tab">
                                        <div className="icons-container">
                                            <div className="icons-content d-flex justify-content-between">
                                                <div className="icons-content-left d-flex">
                                                    <div className="IconFeature icon">
                                                        <img src={IconFeature} className="img-fluid" alt="Download Contacts" onClick={() => { getReportCSV(); setRefresh(!refresh) }} />
                                                    </div>
                                                </div>
                                                <div className="icons-content-right d-flex">
                                                    <div className={showSearchBar == 0 ? "icon-search icon" : "d-none"} style={{ transition: "0.3s" }}>
                                                        <img src={Group49} className="img-fluid" alt="" onClick={() => setShowSearchBar(1)} />
                                                    </div>
                                                    <div id="toggle-search-bar" className={showSearchBar ? "d-flex" : "d-none"}>
                                                        <input class="form-control me-2" type="search" placeholder={t("Search number")} id="search1" aria-label="Search" />
                                                        <button class="search-button-btn" onClick={() => { setTerm(document.getElementById('search1').value); setRefresh(!refresh) }}>Search</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="recipient-activities-table">
                                            <div className="row">
                                                <div className="col-md-12  table-responsive">
                                                    <table className="em-table align-middle table">
                                                        <thead>
                                                            <tr>
                                                                <th scope='col'>{t('Sr.')}</th>
                                                                <th scope="col">{t('Contact Name')}</th>
                                                                <th scope="col">{t('Contact Number')}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {sent_to.length ?
                                                                sent_to.map((contact, index) =>
                                                                    <tr key={index}>
                                                                        <td>{(pageNumber1 - 1) * perPage + index + 1}</td>
                                                                        <td className="text-capitalize">
                                                                            {contact.first_name + " " + contact.last_name}
                                                                        </td>
                                                                        <td>
                                                                            {contact.number}
                                                                        </td>
                                                                    </tr>
                                                                ) : (
                                                                    <tr>
                                                                        <td className="text-center" colSpan="3">
                                                                            {t('No Contacts Found')}
                                                                        </td>
                                                                    </tr>
                                                                )}
                                                        </tbody>
                                                    </table>
                                                    {sent_to.length ?
                                                        <>
                                                            {/* pagination starts here */}
                                                            <div className="mt-2">
                                                                <Pagination
                                                                    activePage={pageNumber1}
                                                                    itemsCountPerPage={perPage}
                                                                    totalItemsCount={totalItems1}
                                                                    pageRangeDisplayed={pageRange}
                                                                    onChange={(e) => { setPageNumber(e); setPageNumber1(e); setModule(1); setTerm(""); setRefresh(!refresh) }}
                                                                />
                                                            </div>
                                                            {/* pagination ends here */}
                                                        </>
                                                        : ""}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="Delivered" role="tabpanel" aria-labelledby="profile-tab">
                                        <div className="icons-container">
                                            <div className="icons-content d-flex justify-content-between">
                                                <div className="icons-content-left d-flex">
                                                    <div className="IconFeature icon">
                                                        <img src={IconFeature} className="img-fluid" alt="Download Contacts" onClick={() => { getReportCSV(); setRefresh(!refresh) }} />
                                                    </div>
                                                </div>
                                                <div className="icons-content-right d-flex">
                                                    <div className={showSearchBar == 0 ? "icon-search icon" : "d-none"} style={{ transition: "0.3s" }}>
                                                        <img src={Group49} className="img-fluid" alt="" onClick={() => setShowSearchBar(1)} />
                                                    </div>
                                                    <div id="toggle-search-bar" className={showSearchBar ? "d-flex" : "d-none"}>
                                                        <input class="form-control me-2" type="search" placeholder={t("Search number")} id="search2" aria-label="Search" />
                                                        <button class="search-button-btn" onClick={() => { setTerm(document.getElementById('search2').value); setRefresh(!refresh) }}>Search</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="recipient-activities-table">
                                            <div className="row">
                                                <div className="col-md-12 table-responsive">
                                                    <table className="em-table align-middle table">
                                                        <thead>
                                                            <tr>
                                                                <th scope='col'>{t('Sr.')}</th>
                                                                <th scope="col">{t('Contact Name')}</th>
                                                                <th scope="col">{t('Contact Number')}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {successes.length ?
                                                                successes.map((contact, index) =>
                                                                    <tr key={index}>
                                                                        <td>{(pageNumber2 - 1) * perPage + index + 1}</td>
                                                                        <td className="text-capitalize">
                                                                            {contact.first_name + " " + contact.last_name}
                                                                        </td>
                                                                        <td>
                                                                            {contact.number}
                                                                        </td>
                                                                    </tr>
                                                                ) : (
                                                                    <tr>
                                                                        <td className="text-center" colSpan="3">
                                                                            {t('No Contacts Found')}
                                                                        </td>
                                                                    </tr>
                                                                )}
                                                        </tbody>
                                                    </table>
                                                    {successes.length ?
                                                        <>
                                                            {/* pagination starts here */}
                                                            <div className="mt-2">
                                                                <Pagination
                                                                    activePage={pageNumber2}
                                                                    itemsCountPerPage={perPage}
                                                                    totalItemsCount={totalItems2}
                                                                    pageRangeDisplayed={pageRange}
                                                                    onChange={(e) => { setPageNumber(e); setPageNumber2(e); setModule(2); setTerm(""); setRefresh(!refresh) }}
                                                                />
                                                            </div>
                                                            {/* pagination ends here */}
                                                        </>
                                                        : ""}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="Failed" role="tabpanel" aria-labelledby="profile-tab">
                                        <div className="icons-container">
                                            <div className="icons-content d-flex justify-content-between">
                                                <div className="icons-content-left d-flex">
                                                    <div className="IconFeature icon">
                                                        <img src={IconFeature} className="img-fluid" alt="Download Contacts" onClick={() => { getReportCSV(); setRefresh(!refresh) }} />
                                                    </div>
                                                </div>
                                                <div className="icons-content-right d-flex">
                                                    <div className={showSearchBar == 0 ? "icon-search icon" : "d-none"} style={{ transition: "0.3s" }}>
                                                        <img src={Group49} className="img-fluid" alt="" onClick={() => setShowSearchBar(1)} />
                                                    </div>
                                                    <div id="toggle-search-bar" className={showSearchBar ? "d-flex" : "d-none"}>
                                                        <input class="form-control me-2" type="search" placeholder={t("Search number")} id="search2" aria-label="Search" />
                                                        <button class="search-button-btn" onClick={() => { setTerm(document.getElementById('search2').value); setRefresh(!refresh) }}>Search</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="recipient-activities-table">
                                            <div className="row">
                                                <div className="col-md-12 table-responsive">
                                                    <table className="em-table align-middle table">
                                                        <thead>
                                                            <tr>
                                                                <th scope='col'>{t('Sr.')}</th>
                                                                <th scope="col">{t('Contact Name')}</th>
                                                                <th scope="col">{t('Contact Number')}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {fails.length ?
                                                                fails.map((contact, index) =>
                                                                    <tr key={index}>
                                                                        <td>{(pageNumber3 - 1) * perPage + index + 1}</td>
                                                                        <td className="text-capitalize">
                                                                            {contact.first_name + " " + contact.last_name}
                                                                        </td>
                                                                        <td>
                                                                            {contact.number}
                                                                        </td>
                                                                    </tr>
                                                                ) : (
                                                                    <tr>
                                                                        <td className="text-center" colSpan="3">
                                                                            {t('No Contacts Found')}
                                                                        </td>
                                                                    </tr>
                                                                )}
                                                        </tbody>
                                                    </table>
                                                    {fails.length ?
                                                        <>
                                                            {/* pagination starts here */}
                                                            <div className="mt-2">
                                                                <Pagination
                                                                    activePage={pageNumber3}
                                                                    itemsCountPerPage={perPage}
                                                                    totalItemsCount={totalItems3}
                                                                    pageRangeDisplayed={pageRange}
                                                                    onChange={(e) => { setPageNumber(e); setPageNumber3(e); setModule(3); setTerm(""); setRefresh(!refresh) }}
                                                                />
                                                            </div>
                                                            {/* pagination ends here */}
                                                        </>
                                                        : ""}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <div className="btns-holder right-btns d-flex flex-row-reverse pt-3 pt-xxl-5">
                <button className="btn btn-secondary ms-3 mb-3" onClick={() => props.back()}>
                    <span>{t('Back')}</span>
                </button>
            </div>
        </>
    );
};

export default withTranslation()(ReportHistory);
