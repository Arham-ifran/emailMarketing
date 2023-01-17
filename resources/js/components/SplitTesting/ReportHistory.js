import React, { useEffect, useState, useRef } from "react";
import {
    Container,
    Form,
    Row,
    Col,
    Button,
    Modal,
    Table,
    Badge,
} from "react-bootstrap";
import Sender from "../../assets/images/Image4.png";
import IconFeature from "../../assets/images/Icon feather-download.svg";
import Union5 from "../../assets/images/Union 5.svg";
import Group49 from "../../assets/images/Group 49.svg";
import {
    LineChart,
    PieChart,
    Pie,
    Line,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip,
    Legend,
    ResponsiveContainer,
    Cell,
    BarChart,
    Bar,
} from "recharts";

import Spinner from "../includes/spinner/Spinner";
import Moment from "react-moment";
import moment from 'moment-timezone';
import { CSVLink, CSVDownload } from "react-csv";
import html2canvas from "html2canvas";
import Pagination from "react-js-pagination";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faSpinner } from "@fortawesome/free-solid-svg-icons";
import { withTranslation } from 'react-i18next';
const ReportHistory = (props, { history, back }) => {
    const { t } = props;
    const [locationMap, setLocationMap] = useState(1);
    const [showSearchBar, setShowSearchBar] = useState(0);

    const [loading, setLoading] = useState(false);
    const [errors, setErrors] = useState([]);
    const [refresh, setRefresh] = useState(0);

    const [term, setTerm] = useState("");
    const [campaign, setCampaign] = useState([]);
    const [reports, setReports] = useState([]);
    const [clicks, setClicks] = useState([]);
    const [clickLogs, setClickLogs] = useState([]);
    const [opens, setOpens] = useState([]);
    const [openLogs, setOpenLogs] = useState([]);
    const [showingContactDetailsFor, setShowingContactDetailsFor] = useState([]);
    const [sent_to, setSent_to] = useState([]);
    const [all_sent_to, setAll_sent_to] = useState([]);
    const [successes, setSuccesses] = useState([]);
    const [fails, setFails] = useState([]);
    const [bounces, setBounces] = useState([]);
    const [unopeners, setUnopeners] = useState([]);
    const [unSubscribers, setUnSubscribers] = useState([]);
    const [opensByTimedata, setOpensByTimedata] = useState([]);
    // tab1 data
    const [report, setReport] = useState([]);
    const [totalOpens, setTotalOpens] = useState([]);
    const [totalSent, setTotalSent] = useState([]);
    const [totalSuccess, setTotalSuccess] = useState([]);
    const [totalFails, setTotalFails] = useState([]);
    const [totalBounces, setTotalBounces] = useState([]);
    const [totalUniqueOpens, setTotalUniqueOpens] = useState([]);
    const [totalUniqueClicks, setTotalUniqueClicks] = useState([]);
    const [totalUnSubscribers, setTotalUnSubscribers] = useState([]);
    const [byContent, setByContent] = useState(false);
    const [split, setSplit] = useState(false);
    const [splitData, setSplitData] = useState([]);

    // for dynamic sections
    const [showContactOpenDetails, setShowContactOpenDetails] = useState(0);

    const getSubject = (subject, num) => {
        setLoading(true);
        axios
            .get("/api/get-split-subject/" + subject + "?lang=" + localStorage.lang)
            .then((response) => {
                setLoading(false);
                if (num == 1) document.getElementById("splitDisplay1").innerHTML = "<b>Split Subject:  </b>" + response.data.split_subject
                else if (num == 2) document.getElementById("splitDisplay2").innerHTML = "<b>Split Subject:  </b>" + response.data.split_subject
            })
            .catch((error) => {
                setLoading(false);
            });
    };

    const getContent = (content, num) => {
        setLoading(true);
        axios
            .get(`/api/get-campaign-template/${content}` + '?lang=' + localStorage.lang)
            .then((response) => {
                setLoading(false);
                if (num == 1)
                    document.getElementById(
                        "splitDisplay1"
                    ).innerHTML = `<div id="teplateimg1" class="text-center"> Content: <br> <img class="img-thubnail" style="max-height: 200px;" src="${response.data.image}" /> </div>`;
                else if (num == 2)
                    document.getElementById(
                        "splitDisplay2"
                    ).innerHTML = `<div id="teplateimg2" class="text-center"> Content: <br> <img class="img-thubnail" style="max-height: 200px;" src="${response.data.image}" /> </div>`;
            })
            .catch((error) => {
                setLoading(false);
            });
    };

    const getReport = (campId) => {
        setLoading(true);
        axios
            .get(
                "/api/get-email-campaign-report/" +
                campId +
                "/" +
                props.history +
                "?page=" +
                pageNumber +
                "&module=" +
                module +
                "&search=" +
                term +
                "&csv=0" +
                "&lang=" + localStorage.lang
            )
            .then((response) => {
                setLoading(false);
                const received_data = response.data;
                setCampaign(received_data.campaign);
                setReports(received_data.reports);
                setOpens(received_data.opensData);
                setTotalItems4(received_data.totalOpens);
                setClickLogs(received_data.clickLogs);
                setTotalItems7(received_data.totalClicks);
                setClicks(received_data.clicksData);
                setTotalItems5(received_data.totalUniqueClicks);
                setSent_to(received_data.sent_to);
                setAll_sent_to(received_data.all_sent_to);
                setTotalItems1(received_data.totalSent);
                setSuccesses(received_data.success);
                setTotalItems2(received_data.totalSuccesses);
                setFails(received_data.fail);
                setTotalItems22(received_data.totalFails);
                setBounces(received_data.bounces);
                setTotalItems8(received_data.totalBounces);
                setUnopeners(received_data.unopeners);
                setTotalItems3(
                    received_data.totalSuccesses -
                    received_data.totalUniqueOpens
                );
                setUnSubscribers(received_data.unsubscribers);
                setTotalItems6(received_data.totalUnsubscribers);
                // tab1 data
                setReport(received_data.report);
                setTotalOpens(received_data.totalOpens);
                setTotalSent(received_data.totalSent);
                setTotalSuccess(received_data.totalSuccesses);
                setTotalFails(received_data.totalFails);
                setTotalBounces(received_data.totalBounces);
                setTotalUniqueOpens(received_data.totalUniqueOpens);
                setTotalUniqueClicks(received_data.totalUniqueClicks);
                setTotalUnSubscribers(received_data.totalUnsubscribers);
                setSplitData(received_data.splitData);

                // opens chart data
                const grouped = received_data.opens.map((row) => {
                    var temp = Object.assign({}, row);
                    temp.created_at = temp.created_at.substring(0, 10);
                    return temp;
                });
                var grp = [];
                grouped.forEach((item, index) => {
                    if (typeof grp[item.created_at] == "undefined") {
                        grp[item.created_at] = [];
                    }
                    grp[item.created_at].push(item);
                });
                var temp = [];
                var keys = Object.keys(grp);
                for (var i = 0; i < Object.keys(grp).length; i++) {
                    temp.push({
                        name: keys[i],
                        opens: grp[keys[i]].length,
                    });
                }
                setOpensByTimedata(temp);
                // opens chart data done

                if (module == 9) {
                    var split = [
                        ...new Map(
                            received_data.all_sent_to.map((item) => [
                                item["id"],
                                item,
                            ])
                        ).values(),
                    ];
                    if (split[0].pivot.content_id) {
                        // by content
                        setByContent(true);
                        setSplit([
                            ...new Set(split.map((item) => item.pivot.content_id)),
                        ]);
                    } else {
                        // by subject
                        setByContent(false);
                        setSplit([
                            ...new Set(split.map((item) => item.pivot.subject_id)),
                        ]);
                    }
                    if (received_data.campaign.split_test_param == 1) {
                        // subject
                        const sub1 = received_data.campaign.split_subject_1;
                        const sub2 = received_data.campaign.split_subject_2;
                        getSubject(sub1, 1);
                        getSubject(sub2, 2);
                    }
                    if (received_data.campaign.split_test_param == 2) {
                        // content
                        const cont1 = received_data.campaign.split_content_1;
                        const cont2 = received_data.campaign.split_content_2;
                        getContent(cont1, 1);
                        getContent(cont2, 2);
                    }
                }

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
            parseUriSegment.indexOf("split-testing") &&
            parseUriSegment.indexOf("report") != -1
        ) {
            getReport(parseUriSegment[2]);
        }
    }, [refresh]);

    const getReportCSV = () => {
        setLoading(true);
        let parseUriSegment = window.location.pathname.split("/");
        if (
            parseUriSegment.indexOf("split-testing") &&
            parseUriSegment.indexOf("report") != -1
        ) {
            var campId = parseUriSegment[2];
            axios
                .get(
                    "/api/get-email-campaign-report/" +
                    campId +
                    "/" +
                    props.history +
                    "?page=" +
                    pageNumber +
                    "&module=" +
                    module +
                    "&search=" +
                    term +
                    "&csv=1" +
                    "&lang=" + localStorage.lang,
                    { responseType: "blob" }
                )
                .then((response) => {
                    setLoading(false);
                    var fileName = "report-contacts.xlsx";
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
    // // fails
    const [pageNumber22, setPageNumber22] = useState(1);
    const [totalItems22, setTotalItems22] = useState(0);
    // // unopened
    const [pageNumber3, setPageNumber3] = useState(1);
    const [totalItems3, setTotalItems3] = useState(0);
    // // opened
    const [pageNumber4, setPageNumber4] = useState(1);
    const [totalItems4, setTotalItems4] = useState(0);
    // // clicked
    const [pageNumber5, setPageNumber5] = useState(1);
    const [totalItems5, setTotalItems5] = useState(0);
    // // unsubscribed
    const [pageNumber6, setPageNumber6] = useState(1);
    const [totalItems6, setTotalItems6] = useState(0);
    // // clicklogs
    const [pageNumber7, setPageNumber7] = useState(1);
    const [totalItems7, setTotalItems7] = useState(0);
    // // bounces
    const [pageNumber8, setPageNumber8] = useState(1);
    const [totalItems8, setTotalItems8] = useState(0);

    const data01 = [
        {
            name: t("Delivered"),
            value: totalSuccess,
        },
        {
            name: t("Bounces"),
            value: totalBounces,
        },
        {
            name: t("Failed"),
            value: totalFails,
        },
    ];

    const reachColors = ["#24e096", "#FD6A21", "#FFBB28"];

    const DownloadReport = () => {
        setLoading(true);
        document.getElementById("hideInImage").style.display = "none";
        html2canvas(document.getElementById("nav-home")).then((canvas) => {
            const image = canvas.toDataURL("image/png");
            var link = document.createElement("a");
            link.download = "campaign_report.png";
            link.href = canvas.toDataURL();
            link.click();
        });
        document.getElementById("hideInImage").style.display = "block";
        setLoading(false);
    };

    return (
        <>
            {loading &&
                (module == 0 || module == 1 || module == 7 || module == 8) ? (
                <Spinner />
            ) : null}
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
                                onClick={() => {
                                    setModule(0);
                                    setTerm("");
                                    setRefresh(!refresh);
                                }}
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
                                onClick={() => {
                                    setModule(1);
                                    setTerm("");
                                    setRefresh(!refresh);
                                    document.getElementById("home-tab").click();
                                }}
                            >
                                {t('Recipient Activities')}
                            </button>
                            <button
                                className="nav-link"
                                id="nav-contact-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#nav-contact"
                                type="button"
                                role="tab"
                                aria-controls="nav-contact"
                                aria-selected="false"
                                onClick={() => {
                                    setModule(7);
                                    setTerm("");
                                    setRefresh(!refresh);
                                }}
                            >
                                {t('Click Activities')}
                            </button>
                            <button
                                className="nav-link"
                                id="nav-bounce-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#nav-bounce"
                                type="button"
                                role="tab"
                                aria-controls="nav-bounce"
                                aria-selected="false"
                                onClick={() => {
                                    setModule(8);
                                    setTerm("");
                                    setRefresh(!refresh);
                                }}
                            >
                                {t('Bounces')}
                            </button>

                            <button
                                className="nav-link"
                                id="nav-split-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#nav-split"
                                type="button"
                                role="tab"
                                aria-controls="nav-split"
                                aria-selected="false"
                                onClick={() => {
                                    setModule(9);
                                    setTerm("");
                                    setRefresh(!refresh);
                                }}
                            >
                                {t('Split Report')}
                            </button>
                        </div>
                    </nav>
                    <div className="tab-content" id="nav-tabContent">
                        <div
                            className="tab-pane fade show active"
                            id="nav-home"
                            role="tabpanel"
                            aria-labelledby="nav-home-tab"
                        >
                            <div className="icons-container" id="hideInImage">
                                <div className="icons-content d-flex justify-content-end mb-2">
                                    <div className="icons-content-left d-flex">
                                        <div className="IconFeature icon">
                                            <div
                                                onClick={() => DownloadReport()}
                                            >
                                                <img
                                                    src={IconFeature}
                                                    className="img-fluid"
                                                    alt=""
                                                />
                                            </div>
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
                                        <p>{t('Total Emails Sent')}</p>
                                        <span>
                                            <Moment format="DD MMMM YYYY hh:mm:ss">
                                                {moment.tz(moment(
                                                    _.isEmpty(report)
                                                        ? campaign.created_at
                                                        : report.created_at
                                                ).utc(), localStorage.timezone)}
                                            </Moment>
                                        </span>
                                    </div>
                                    <div className="rt-progress">
                                        <div class="progress">
                                            <div
                                                class="progress-bar bg-success"
                                                role="progressbar"
                                                style={{
                                                    width:
                                                        totalSent == 0
                                                            ? 0
                                                            : ((totalSuccess /
                                                                totalSent) *
                                                                100).toFixed(2) +
                                                            "%",
                                                    ariaValuemax: "100",
                                                }}
                                            ></div>
                                        </div>
                                        <span>
                                            {totalSent == 0
                                                ? 0
                                                : ((totalSuccess / totalSent) *
                                                    100).toFixed(2)}
                                            {t('% Delivered')}
                                        </span>
                                    </div>
                                    <div className="rt-dlvr">
                                        <div className="dlvr">
                                            <div className="delivered"></div>
                                            <div className="rt-desc">
                                                <p>
                                                    {t('Delivered')}{" "}
                                                    {totalSent == 0
                                                        ? 0
                                                        : ((totalSuccess /
                                                            totalSent) *
                                                            100).toFixed(2)}
                                                    %
                                                </p>
                                                <p>{totalSuccess} {t('Contacts')}</p>
                                            </div>
                                        </div>
                                        <div className="dlvr">
                                            <div className="bounced"></div>
                                            <div className="rt-desc">
                                                <p>
                                                    {t('Bounces')}{" "}
                                                    {totalSent == 0
                                                        ? 0
                                                        : ((totalBounces /
                                                            totalSent) *
                                                            100).toFixed(2)}
                                                    %
                                                </p>
                                                <p>{totalBounces} {t('Contacts')}</p>
                                            </div>
                                        </div>
                                        <div className="dlvr">
                                            <div className="unsent"></div>
                                            <div className="rt-desc">
                                                <p>
                                                    {t('Failed')}{" "}
                                                    {totalSent == 0
                                                        ? 0
                                                        : ((totalFails /
                                                            totalSent) *
                                                            100).toFixed(2)}
                                                    %
                                                </p>
                                                <p>{totalFails} {t('Contacts')}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="rt-prog">
                                        <div class="progress">
                                            <div
                                                class="progress-bar bg-custom"
                                                role="progressbar"
                                                style={{
                                                    width:
                                                        totalSent == 0
                                                            ? 0
                                                            : ((totalUniqueOpens /
                                                                totalSuccess) *
                                                                100).toFixed(2) +
                                                            "%",
                                                    ariaValuemax: "100",
                                                }}
                                            ></div>
                                        </div>
                                        <span>
                                            {totalSent == 0
                                                ? 0
                                                : ((totalUniqueOpens /
                                                    totalSuccess) *
                                                    100).toFixed(2)}
                                            %
                                        </span>
                                    </div>
                                    <div className="rt-dlvr">
                                        <div className="dlvr">
                                            <div className="open"></div>
                                            <div className="rt-desc">
                                                <p>
                                                    {t('Unique Opens')}{" "}
                                                    {totalSent == 0
                                                        ? 0
                                                        : ((totalUniqueOpens /
                                                            totalSuccess) *
                                                            100).toFixed(2)}
                                                    %
                                                </p>
                                                <p>
                                                    {totalUniqueOpens} {t('Contacts')}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="dlvr">
                                            <div className="clicks"></div>
                                            <div className="rt-desc">
                                                <p>
                                                    {t('Unique Clicks')}{" "}
                                                    {totalSent == 0
                                                        ? 0
                                                        : ((totalUniqueClicks /
                                                            totalSuccess) *
                                                            100).toFixed(2)}
                                                    %
                                                </p>
                                                <p>
                                                    {totalUniqueClicks} {t('Contacts')}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="dlvr">
                                            <div className="unopen"></div>
                                            <div className="rt-desc">
                                                <p>
                                                    {t('Unopened')}{" "}
                                                    {totalSent == 0
                                                        ? 0
                                                        : (((totalSent -
                                                            totalUniqueOpens) /
                                                            totalSuccess) *
                                                            100).toFixed(2)}
                                                    %
                                                </p>
                                                <p>
                                                    {totalSent -
                                                        totalUniqueOpens}{" "}
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
                                            <p>{totalSent}</p>
                                            <p>{t('Total Reach')}</p>
                                        </div>
                                    </div>
                                    <div class="abt-email-container">
                                        <div className="abt-email">
                                            <div className="about-mail">
                                                <div className="abt-mail"> </div>
                                                <p>{t("Email")}</p>
                                            </div>
                                            <div className="abt-views">
                                                <p>{totalOpens} {t('Views')}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="row">
                                <div className="campaign-graph-holder p-outerbox reach-graph">
                                    <div className="d-flex -space -breaker">
                                        <h5>{t('Opens by Time')}</h5>
                                    </div>
                                    <ResponsiveContainer
                                        width="100%"
                                        height={200}
                                    >
                                        <LineChart
                                            width={500}
                                            height={300}
                                            data={opensByTimedata}
                                            margin={{
                                                top: 5,
                                                right: 30,
                                                left: 20,
                                                bottom: 5,
                                            }}
                                        >
                                            <CartesianGrid strokeDasharray="3 3" />
                                            <XAxis dataKey="name" />
                                            <YAxis />
                                            <Tooltip />
                                            <Legend />
                                            <Line
                                                type="monotone"
                                                dataKey="opens"
                                                stroke="#24e096"
                                            />
                                        </LineChart>
                                    </ResponsiveContainer>
                                </div>
                            </div>
                            {/* <div className="row reach-graph">
                                <div className="map">
                                    <div className="about-map">
                                        <h5>Opens by Location</h5>
                                    </div>
                                    <div className="map-button">
                                        <div
                                            className="feather"
                                            onClick={() => setLocationMap(1)}
                                        >
                                            <img src={Feather} />
                                        </div>
                                        <div
                                            className="mine"
                                            onClick={() => setLocationMap(0)}
                                        >
                                            <img src={Mine} />
                                        </div>
                                    </div>
                                </div>
                                <div className="map-image">
                                    {locationMap ? (
                                        <img
                                            className="img-fluid"
                                            src={Group37}
                                        />
                                    ) : (
                                        <div className="status-table">
                                            <div className="table-responsive">
                                                <Table className="align-middle em-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Country</th>
                                                            <th>Contact</th>
                                                            <th>Clicks</th>
                                                            <th>Opens</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td className="text-capitalize">
                                                                armash
                                                            </td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                    </tbody>
                                                </Table>
                                            </div>
                                        </div>
                                    )}
                                    <p>
                                        Note: Email opens from unknown location
                                        - 9
                                    </p>
                                </div>
                            </div> */}
                            <div className="row d-flex flex-column p-outerbox reach-graph">
                                <div className="d-felx -space -breaker">
                                    <h5>{t('Subject and Sender details')}</h5>
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
                                                    <p>{t('Sender Name')} :</p>
                                                </div>
                                                <div className="d-flex">
                                                    <p>{campaign.sender_name}</p>
                                                </div>
                                            </div>
                                            <div className="d-flex mb-2  justify-content-sm-start">
                                                <div className="d-flex fixed-w">
                                                    <p>{t('Sender Address')} :</p>
                                                </div>
                                                <div className="d-flex">
                                                    <p>{campaign.sender_email}</p>
                                                </div>
                                            </div>
                                            {campaign.reply_to_email ?
                                                <div className="d-flex mb-2  justify-content-sm-start">
                                                    <div className="d-flex fixed-w">
                                                        <p>{t('Reply-to Address')} :</p>
                                                    </div>
                                                    <div className="d-flex">
                                                        <p>{campaign.reply_to_email}</p>
                                                    </div>
                                                </div>
                                                : null
                                            }
                                            <div className="d-flex mb-2  justify-content-sm-start">
                                                <div className="d-flex fixed-w">
                                                    <p>{t('Created on')} :</p>
                                                </div>
                                                <div className="d-flex">
                                                    <p><Moment format="DD MMMM YYYY hh:mm:ss">
                                                        {moment.tz(moment(
                                                            _.isEmpty(report)
                                                                ? campaign.created_at
                                                                : report.created_at
                                                        ).utc(), localStorage.timezone)}
                                                    </Moment>
                                                    </p>
                                                </div>
                                            </div>
                                            <div className="d-flex mb-2  justify-content-sm-start">
                                                <div className="d-flex fixed-w">
                                                    <p>{t('Sent on')} :</p>
                                                </div>
                                                <div className="d-flex">
                                                    <p>
                                                        <Moment format="DD MMMM YYYY hh:mm:ss">
                                                            {moment.tz(moment(
                                                                _.isEmpty(report)
                                                                    ? campaign.updated_at
                                                                    : report.updated_at
                                                            ).utc(), localStorage.timezone)}
                                                        </Moment>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            className="tab-pane fade"
                            id="nav-profile"
                            role="tabpanel"
                            aria-labelledby="nav-profile-tab"
                        >
                            <div className="recipient-activities-tabs">
                                <div className="tabs-container">
                                    <ul
                                        class="nav nav-tabs justify-content-sm-between justify-content-around"
                                        id="myTab"
                                        role="tablist"
                                        onClick={() => {
                                            setShowContactOpenDetails(0);
                                            setShowingContactDetailsFor([]);
                                        }}
                                    >
                                        <li
                                            class="nav-item"
                                            role="presentation"
                                        >
                                            <button
                                                class="nav-link active"
                                                id="home-tab"
                                                data-bs-toggle="tab"
                                                data-bs-target="#Sent"
                                                type="button"
                                                role="tab"
                                                aria-controls="Sent"
                                                aria-selected="true"
                                                onClick={() => {
                                                    setModule(1);
                                                    setTerm("");
                                                    setRefresh(!refresh);
                                                }}
                                            >
                                                {totalSent}
                                                <p>{t('Sent')}</p>
                                            </button>
                                        </li>
                                        <li
                                            class="nav-item"
                                            role="presentation"
                                        >
                                            <button
                                                class="nav-link"
                                                id="profile-tab"
                                                data-bs-toggle="tab"
                                                data-bs-target="#Delivered"
                                                type="button"
                                                role="tab"
                                                aria-controls="Delivered"
                                                aria-selected="false"
                                                onClick={() => {
                                                    setModule(2);
                                                    setTerm("");
                                                    setRefresh(!refresh);
                                                }}
                                            >
                                                {totalSuccess}
                                                <p>{t('Delivered')}</p>
                                            </button>
                                        </li>
                                        <li
                                            class="nav-item"
                                            role="presentation"
                                        >
                                            <button
                                                class="nav-link"
                                                id="profile-tab"
                                                data-bs-toggle="tab"
                                                data-bs-target="#Failed"
                                                type="button"
                                                role="tab"
                                                aria-controls="Failed"
                                                aria-selected="false"
                                                onClick={() => {
                                                    setModule(22);
                                                    setTerm("");
                                                    setRefresh(!refresh);
                                                }}
                                            >
                                                {totalFails}
                                                <p>{t('Failed')}</p>
                                            </button>
                                        </li>
                                        <li
                                            class="nav-item"
                                            role="presentation"
                                        >
                                            <button
                                                class="nav-link"
                                                id="contact-tab"
                                                data-bs-toggle="tab"
                                                data-bs-target="#Unopened"
                                                type="button"
                                                role="tab"
                                                aria-controls="Unopened"
                                                aria-selected="false"
                                                onClick={() => {
                                                    setModule(3);
                                                    setTerm("");
                                                    setRefresh(!refresh);
                                                }}
                                            >
                                                {totalSuccess -
                                                    totalUniqueOpens}
                                                <p>{t('Unopened')}</p>
                                            </button>
                                        </li>
                                        <li
                                            class="nav-item"
                                            role="presentation"
                                        >
                                            <button
                                                class="nav-link"
                                                id="contact-tab"
                                                data-bs-toggle="tab"
                                                data-bs-target="#Opened"
                                                type="button"
                                                role="tab"
                                                aria-controls="Opened"
                                                aria-selected="false"
                                                onClick={() => {
                                                    setModule(4);
                                                    setTerm("");
                                                    setRefresh(!refresh);
                                                }}
                                            >
                                                {totalUniqueOpens}
                                                <p>{t('Opened')}</p>
                                            </button>
                                        </li>
                                        <li
                                            class="nav-item"
                                            role="presentation"
                                        >
                                            <button
                                                class="nav-link"
                                                id="contact-tab"
                                                data-bs-toggle="tab"
                                                data-bs-target="#Clicked"
                                                type="button"
                                                role="tab"
                                                aria-controls="Clicked"
                                                aria-selected="false"
                                                onClick={() => {
                                                    setModule(5);
                                                    setTerm("");
                                                    setRefresh(!refresh);
                                                }}
                                            >
                                                {totalUniqueClicks}
                                                <p>{t('Clicked')}</p>

                                            </button>
                                        </li>
                                        <li
                                            class="nav-item"
                                            role="presentation"
                                        >
                                            <button
                                                class="nav-link"
                                                id="contact-tab"
                                                data-bs-toggle="tab"
                                                data-bs-target="#Unsubscribes"
                                                type="button"
                                                role="tab"
                                                aria-controls="Unsubscribes"
                                                aria-selected="false"
                                                onClick={() => {
                                                    setModule(6);
                                                    setTerm("");
                                                    setRefresh(!refresh);
                                                }}
                                            >
                                                {totalUnSubscribers}
                                                <p>{t('Unsubscribes')}</p>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tab-content" id="myTabContent">
                                    {loading &&
                                        (module == 1 ||
                                            module == 2 ||
                                            module == 3 ||
                                            module == 4 ||
                                            module == 5 ||
                                            module == 6) ? (
                                        <FontAwesomeIcon
                                            icon={faSpinner}
                                            spin
                                        />
                                    ) : null}

                                    <div
                                        class="tab-pane fade show active"
                                        id="Sent"
                                        role="tabpanel"
                                        aria-labelledby="home-tab"
                                    >
                                        <div className="icons-container">
                                            <div className="icons-content d-flex justify-content-between">
                                                <div className="icons-content-left d-flex">
                                                    <div className="IconFeature icon">
                                                        <img
                                                            src={IconFeature}
                                                            className="img-fluid"
                                                            alt="Download Contacts"
                                                            onClick={() => {
                                                                getReportCSV();
                                                                setRefresh(
                                                                    !refresh
                                                                );
                                                            }}
                                                        />
                                                    </div>
                                                </div>
                                                <div className="icons-content-right d-flex">
                                                    <div
                                                        className={
                                                            showSearchBar == 0
                                                                ? "icon-search icon"
                                                                : "d-none"
                                                        }
                                                        style={{
                                                            transition: "0.3s",
                                                        }}
                                                    >
                                                        <img
                                                            src={Group49}
                                                            className="img-fluid"
                                                            alt=""
                                                            onClick={() =>
                                                                setShowSearchBar(
                                                                    1
                                                                )
                                                            }
                                                        />
                                                    </div>
                                                    <div
                                                        id="toggle-search-bar"
                                                        className={
                                                            showSearchBar
                                                                ? "d-flex"
                                                                : "d-none"
                                                        }
                                                    >
                                                        <input
                                                            class="form-control me-2"
                                                            type="search"
                                                            placeholder={t("Search email address")}
                                                            id="search1"
                                                            aria-label="Search"
                                                        />
                                                        <button
                                                            class="search-button-btn"
                                                            onClick={() => {
                                                                setTerm(
                                                                    document.getElementById(
                                                                        "search1"
                                                                    ).value
                                                                );
                                                                setRefresh(
                                                                    !refresh
                                                                );
                                                            }}
                                                        >
                                                            {t('Search')}
                                                        </button>
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
                                                                <th scope="col">
                                                                    {t('Sr.')}
                                                                </th>
                                                                <th scope="col">
                                                                    {t('Contact Name')}
                                                                </th>
                                                                <th scope="col">
                                                                    {t('Contact Email')}
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {sent_to.length ? (
                                                                sent_to.map(
                                                                    (
                                                                        contact,
                                                                        index
                                                                    ) => (
                                                                        <tr>
                                                                            <td>
                                                                                {(pageNumber1 -
                                                                                    1) *
                                                                                    perPage +
                                                                                    index +
                                                                                    1}
                                                                            </td>
                                                                            <td className="text-capitalize">
                                                                                {contact.first_name +
                                                                                    " " +
                                                                                    contact.last_name}
                                                                            </td>
                                                                            <td>
                                                                                {
                                                                                    contact.email
                                                                                }
                                                                            </td>
                                                                        </tr>
                                                                    )
                                                                )
                                                            ) : (
                                                                <tr>
                                                                    <td
                                                                        className="text-center"
                                                                        colSpan="3"
                                                                    >
                                                                        {t('No Contacts Found')}

                                                                    </td>
                                                                </tr>
                                                            )}
                                                        </tbody>
                                                    </table>
                                                    {sent_to.length ? (
                                                        <>
                                                            {/* pagination starts here */}
                                                            <div className="mt-2">
                                                                <Pagination
                                                                    activePage={
                                                                        pageNumber1
                                                                    }
                                                                    itemsCountPerPage={
                                                                        perPage
                                                                    }
                                                                    totalItemsCount={
                                                                        totalItems1
                                                                    }
                                                                    pageRangeDisplayed={
                                                                        pageRange
                                                                    }
                                                                    onChange={(
                                                                        e
                                                                    ) => {
                                                                        setPageNumber(
                                                                            e
                                                                        );
                                                                        setPageNumber1(
                                                                            e
                                                                        );
                                                                        setModule(
                                                                            1
                                                                        );
                                                                        setTerm(
                                                                            ""
                                                                        );
                                                                        setRefresh(
                                                                            !refresh
                                                                        );
                                                                    }}
                                                                />
                                                            </div>
                                                            {/* pagination ends here */}
                                                        </>
                                                    ) : (
                                                        ""
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="tab-pane fade"
                                        id="Delivered"
                                        role="tabpanel"
                                        aria-labelledby="profile-tab"
                                    >
                                        <div className="icons-container">
                                            <div className="icons-content d-flex justify-content-between">
                                                <div className="icons-content-left d-flex">
                                                    <div className="IconFeature icon">
                                                        <img
                                                            src={IconFeature}
                                                            className="img-fluid"
                                                            alt="Download Contacts"
                                                            onClick={() => {
                                                                getReportCSV();
                                                                setRefresh(
                                                                    !refresh
                                                                );
                                                            }}
                                                        />
                                                    </div>
                                                </div>
                                                <div className="icons-content-right d-flex">
                                                    <div
                                                        className={
                                                            showSearchBar == 0
                                                                ? "icon-search icon"
                                                                : "d-none"
                                                        }
                                                        style={{
                                                            transition: "0.3s",
                                                        }}
                                                    >
                                                        <img
                                                            src={Group49}
                                                            className="img-fluid"
                                                            alt=""
                                                            onClick={() =>
                                                                setShowSearchBar(
                                                                    1
                                                                )
                                                            }
                                                        />
                                                    </div>
                                                    <div
                                                        id="toggle-search-bar"
                                                        className={
                                                            showSearchBar
                                                                ? "d-flex"
                                                                : "d-none"
                                                        }
                                                    >
                                                        <input
                                                            class="form-control me-2"
                                                            type="search"
                                                            placeholder={t("Search email address")}
                                                            id="search2"
                                                            aria-label="Search"
                                                        />
                                                        <button
                                                            class="search-button-btn"
                                                            onClick={() => {
                                                                setTerm(
                                                                    document.getElementById(
                                                                        "search2"
                                                                    ).value
                                                                );
                                                                setRefresh(
                                                                    !refresh
                                                                );
                                                            }}
                                                        >
                                                            {t('Search')}

                                                        </button>
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
                                                                <th scope="col">
                                                                    {t('Sr.')}
                                                                </th>
                                                                <th scope="col">
                                                                    {t('Contact Name')}
                                                                </th>
                                                                <th scope="col">
                                                                    {t('Contact Email')}

                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {successes.length ? (
                                                                successes.map(
                                                                    (
                                                                        contact,
                                                                        index
                                                                    ) => (
                                                                        <tr>
                                                                            <td>
                                                                                {(pageNumber2 -
                                                                                    1) *
                                                                                    perPage +
                                                                                    index +
                                                                                    1}
                                                                            </td>
                                                                            <td className="text-capitalize">
                                                                                {contact.first_name +
                                                                                    " " +
                                                                                    contact.last_name}
                                                                            </td>
                                                                            <td>
                                                                                {
                                                                                    contact.email
                                                                                }
                                                                            </td>
                                                                        </tr>
                                                                    )
                                                                )
                                                            ) : (
                                                                <tr>
                                                                    <td
                                                                        className="text-center"
                                                                        colSpan="3"
                                                                    >
                                                                        {t('No Contacts Found')}

                                                                    </td>
                                                                </tr>
                                                            )}
                                                        </tbody>
                                                    </table>
                                                    {successes.length ? (
                                                        <>
                                                            {/* pagination starts here */}
                                                            <div className="mt-2">
                                                                <Pagination
                                                                    activePage={
                                                                        pageNumber2
                                                                    }
                                                                    itemsCountPerPage={
                                                                        perPage
                                                                    }
                                                                    totalItemsCount={
                                                                        totalItems2
                                                                    }
                                                                    pageRangeDisplayed={
                                                                        pageRange
                                                                    }
                                                                    onChange={(
                                                                        e
                                                                    ) => {
                                                                        setPageNumber(
                                                                            e
                                                                        );
                                                                        setPageNumber2(
                                                                            e
                                                                        );
                                                                        setModule(
                                                                            2
                                                                        );
                                                                        setTerm(
                                                                            ""
                                                                        );
                                                                        setRefresh(
                                                                            !refresh
                                                                        );
                                                                    }}
                                                                />
                                                            </div>
                                                            {/* pagination ends here */}
                                                        </>
                                                    ) : (
                                                        ""
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="tab-pane fade"
                                        id="Failed"
                                        role="tabpanel"
                                        aria-labelledby="profile-tab"
                                    >
                                        <div className="icons-container">
                                            <div className="icons-content d-flex justify-content-between">
                                                <div className="icons-content-left d-flex">
                                                    <div className="IconFeature icon">
                                                        <img
                                                            src={IconFeature}
                                                            className="img-fluid"
                                                            alt="Download Contacts"
                                                            onClick={() => {
                                                                getReportCSV();
                                                                setRefresh(
                                                                    !refresh
                                                                );
                                                            }}
                                                        />
                                                    </div>
                                                </div>
                                                <div className="icons-content-right d-flex">
                                                    <div
                                                        className={
                                                            showSearchBar == 0
                                                                ? "icon-search icon"
                                                                : "d-none"
                                                        }
                                                        style={{
                                                            transition: "0.3s",
                                                        }}
                                                    >
                                                        <img
                                                            src={Group49}
                                                            className="img-fluid"
                                                            alt=""
                                                            onClick={() =>
                                                                setShowSearchBar(
                                                                    1
                                                                )
                                                            }
                                                        />
                                                    </div>
                                                    <div
                                                        id="toggle-search-bar"
                                                        className={
                                                            showSearchBar
                                                                ? "d-flex"
                                                                : "d-none"
                                                        }
                                                    >
                                                        <input
                                                            class="form-control me-2"
                                                            type="search"
                                                            placeholder={t("Search email address")}
                                                            id="search2"
                                                            aria-label="Search"
                                                        />
                                                        <button
                                                            class="search-button-btn"
                                                            onClick={() => {
                                                                setTerm(
                                                                    document.getElementById(
                                                                        "search2"
                                                                    ).value
                                                                );
                                                                setRefresh(
                                                                    !refresh
                                                                );
                                                            }}
                                                        >
                                                            {t('Search')}
                                                        </button>
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
                                                                <th scope="col">
                                                                    {t('Sr.')}
                                                                </th>
                                                                <th scope="col">
                                                                    {t('Contact Name')}
                                                                </th>
                                                                <th scope="col">
                                                                    {t('Contact Email')}
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {fails.length ? (
                                                                fails.map(
                                                                    (
                                                                        contact,
                                                                        index
                                                                    ) => (
                                                                        <tr>
                                                                            <td>
                                                                                {(pageNumber22 -
                                                                                    1) *
                                                                                    perPage +
                                                                                    index +
                                                                                    1}
                                                                            </td>
                                                                            <td className="text-capitalize">
                                                                                {contact.first_name +
                                                                                    " " +
                                                                                    contact.last_name}
                                                                            </td>
                                                                            <td>
                                                                                {
                                                                                    contact.email
                                                                                }
                                                                            </td>
                                                                        </tr>
                                                                    )
                                                                )
                                                            ) : (
                                                                <tr>
                                                                    <td
                                                                        className="text-center"
                                                                        colSpan="3"
                                                                    >
                                                                        {t('No Contacts Found')}
                                                                    </td>
                                                                </tr>
                                                            )}
                                                            <tr>
                                                                <td className="text-center" colSpan="3">
                                                                    {loading &&
                                                                        (module == 1 ||
                                                                            module == 2 ||
                                                                            module == 3 ||
                                                                            module == 4 ||
                                                                            module == 5 ||
                                                                            module == 6) ? (
                                                                        <FontAwesomeIcon
                                                                            icon={faSpinner}
                                                                            spin
                                                                        />
                                                                    ) : null}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    {successes.length ? (
                                                        <>
                                                            {/* pagination starts here */}
                                                            <div className="mt-2">
                                                                <Pagination
                                                                    activePage={
                                                                        pageNumber22
                                                                    }
                                                                    itemsCountPerPage={
                                                                        perPage
                                                                    }
                                                                    totalItemsCount={
                                                                        totalItems22
                                                                    }
                                                                    pageRangeDisplayed={
                                                                        pageRange
                                                                    }
                                                                    onChange={(
                                                                        e
                                                                    ) => {
                                                                        setPageNumber(
                                                                            e
                                                                        );
                                                                        setPageNumber22(
                                                                            e
                                                                        );
                                                                        setModule(
                                                                            22
                                                                        );
                                                                        setTerm(
                                                                            ""
                                                                        );
                                                                        setRefresh(
                                                                            !refresh
                                                                        );
                                                                    }}
                                                                />
                                                            </div>
                                                            {/* pagination ends here */}
                                                        </>
                                                    ) : (
                                                        ""
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="tab-pane fade"
                                        id="Opened"
                                        role="tabpanel"
                                        aria-labelledby="contact-tab"
                                    >
                                        <div className="icons-container">
                                            <div className="icons-content d-flex justify-content-between">
                                                <div className="icons-content-left d-flex">
                                                    <div className="IconFeature icon">
                                                        <img
                                                            src={IconFeature}
                                                            className="img-fluid"
                                                            alt="Download Contacts"
                                                            onClick={() => {
                                                                getReportCSV();
                                                                setRefresh(
                                                                    !refresh
                                                                );
                                                            }}
                                                        />
                                                    </div>
                                                </div>
                                                <div className="icons-content-right d-flex">
                                                    <div
                                                        className={
                                                            showSearchBar == 0
                                                                ? "icon-search icon"
                                                                : "d-none"
                                                        }
                                                        style={{
                                                            transition: "0.3s",
                                                        }}
                                                    >
                                                        <img
                                                            src={Group49}
                                                            className="img-fluid"
                                                            alt=""
                                                            onClick={() =>
                                                                setShowSearchBar(
                                                                    1
                                                                )
                                                            }
                                                        />
                                                    </div>
                                                    <div
                                                        id="toggle-search-bar"
                                                        className={
                                                            showSearchBar
                                                                ? "d-flex"
                                                                : "d-none"
                                                        }
                                                    >
                                                        <input
                                                            class="form-control me-2"
                                                            type="search"
                                                            placeholder={t("Search email address")}
                                                            id="search3"
                                                            aria-label="Search"
                                                        />
                                                        <button
                                                            class="search-button-btn"
                                                            onClick={() => {
                                                                setTerm(
                                                                    document.getElementById(
                                                                        "search3"
                                                                    ).value
                                                                );
                                                                setRefresh(
                                                                    !refresh
                                                                );
                                                            }}
                                                        >
                                                            {t('Search')}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="recipient-activities-table">
                                            <div className="row">
                                                <div className={showContactOpenDetails ? "col-md-6 table-responsive" : "col-md-12 table-responsive"}>
                                                    <table className="em-table align-middle table">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">{t('Sr.')}</th>
                                                                <th scope="col">{t('Contact Email')}</th>
                                                                <th scope="col" className="text-start">{t('Total Opens')}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {opens.length ? (
                                                                opens.map(
                                                                    (
                                                                        row,
                                                                        index
                                                                    ) => (
                                                                        <tr
                                                                            className={
                                                                                showingContactDetailsFor.id ==
                                                                                    row
                                                                                        .contact
                                                                                        .id
                                                                                    ? "table-success"
                                                                                    : ""
                                                                            }
                                                                            style={{
                                                                                cursor: "pointer",
                                                                            }}
                                                                            onClick={() => {
                                                                                setShowContactOpenDetails(
                                                                                    1
                                                                                );
                                                                                setOpenLogs(
                                                                                    JSON.parse(
                                                                                        row.opens
                                                                                    )
                                                                                );
                                                                                setShowingContactDetailsFor(
                                                                                    row.contact
                                                                                );
                                                                            }}
                                                                        >
                                                                            <td>
                                                                                {(pageNumber4 -
                                                                                    1) *
                                                                                    perPage +
                                                                                    index +
                                                                                    1}
                                                                            </td>
                                                                            <td className="text-capitalize">
                                                                                {
                                                                                    row
                                                                                        .contact
                                                                                        .email
                                                                                }
                                                                            </td>
                                                                            <td>
                                                                                {
                                                                                    JSON.parse(
                                                                                        row.opens
                                                                                    )
                                                                                        .length
                                                                                }{" "}
                                                                                {t('Opens')}
                                                                            </td>
                                                                            <td className="d-none">
                                                                                {JSON.parse(
                                                                                    row.opens
                                                                                ).map(
                                                                                    (
                                                                                        log
                                                                                    ) => (
                                                                                        <p>
                                                                                            <Moment format="DD MMMM YYYY, h:mm:ss a">
                                                                                                {moment.tz(moment(log).utc(), localStorage.timezone)}
                                                                                            </Moment>
                                                                                        </p>
                                                                                    )
                                                                                )}
                                                                            </td>
                                                                        </tr>
                                                                    )
                                                                )
                                                            ) : (
                                                                <tr>
                                                                    <td className="text-center" colSpan="3">
                                                                        {t('No Contacts Found')}

                                                                    </td>
                                                                </tr>
                                                            )}
                                                        </tbody>
                                                    </table>
                                                    {opens.length ? (
                                                        <>
                                                            {/* pagination starts here */}
                                                            <div className="mt-2">
                                                                <Pagination
                                                                    activePage={
                                                                        pageNumber4
                                                                    }
                                                                    itemsCountPerPage={
                                                                        perPage
                                                                    }
                                                                    totalItemsCount={
                                                                        totalItems4
                                                                    }
                                                                    pageRangeDisplayed={
                                                                        pageRange
                                                                    }
                                                                    onChange={(
                                                                        e
                                                                    ) => {
                                                                        setPageNumber(
                                                                            e
                                                                        );
                                                                        setPageNumber4(
                                                                            e
                                                                        );
                                                                        setModule(
                                                                            4
                                                                        );
                                                                        setTerm(
                                                                            ""
                                                                        );
                                                                        setRefresh(
                                                                            !refresh
                                                                        );
                                                                    }}
                                                                />
                                                            </div>
                                                            {/* pagination ends here */}
                                                        </>
                                                    ) : (
                                                        ""
                                                    )}
                                                </div>

                                                <div
                                                    className={
                                                        showContactOpenDetails
                                                            ? "col-md-6 recipient-activities-table-pagination"
                                                            : "d-none"
                                                    }
                                                >
                                                    <table className="table active-user-table-content">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">
                                                                    {t('Opened by')} -{" "}
                                                                    {
                                                                        showingContactDetailsFor.email
                                                                    }
                                                                </th>
                                                                <th scope="col">
                                                                    <img
                                                                        src={
                                                                            Union5
                                                                        }
                                                                        alt="Close"
                                                                        onClick={() => {
                                                                            setShowContactOpenDetails(
                                                                                0
                                                                            );
                                                                            setShowingContactDetailsFor(
                                                                                []
                                                                            );
                                                                        }}
                                                                        className="img-fluid"
                                                                        style={{
                                                                            cursor: "pointer",
                                                                        }}
                                                                    />
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {openLogs.map(
                                                                (log) => (
                                                                    <p>
                                                                        <Moment format="DD MMMM YYYY, h:mm:ss a">
                                                                            {moment.tz(moment(log).utc(), localStorage.timezone)}
                                                                        </Moment>
                                                                    </p>
                                                                )
                                                            )}
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="tab-pane fade"
                                        id="Unopened"
                                        role="tabpanel"
                                        aria-labelledby="contact-tab"
                                    >
                                        <div className="icons-container">
                                            <div className="icons-content d-flex justify-content-between">
                                                <div className="icons-content-left d-flex">
                                                    <div className="IconFeature icon">
                                                        <img
                                                            src={IconFeature}
                                                            className="img-fluid"
                                                            alt="Download Contacts"
                                                            onClick={() => {
                                                                getReportCSV();
                                                                setRefresh(
                                                                    !refresh
                                                                );
                                                            }}
                                                        />
                                                    </div>
                                                </div>
                                                <div className="icons-content-right d-flex">
                                                    <div
                                                        className={
                                                            showSearchBar == 0
                                                                ? "icon-search icon"
                                                                : "d-none"
                                                        }
                                                        style={{
                                                            transition: "0.3s",
                                                        }}
                                                    >
                                                        <img
                                                            src={Group49}
                                                            className="img-fluid"
                                                            alt=""
                                                            onClick={() =>
                                                                setShowSearchBar(
                                                                    1
                                                                )
                                                            }
                                                        />
                                                    </div>
                                                    <div
                                                        id="toggle-search-bar"
                                                        className={
                                                            showSearchBar
                                                                ? "d-flex"
                                                                : "d-none"
                                                        }
                                                    >
                                                        <input
                                                            class="form-control me-2"
                                                            type="search"
                                                            placeholder={t("Search email address")}
                                                            id="search4"
                                                            aria-label="Search"
                                                        />
                                                        <button
                                                            class="search-button-btn"
                                                            onClick={() => {
                                                                setTerm(
                                                                    document.getElementById(
                                                                        "search4"
                                                                    ).value
                                                                );
                                                                setRefresh(
                                                                    !refresh
                                                                );
                                                            }}
                                                        >
                                                            {t('Search')}
                                                        </button>
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
                                                                <th scope="col">
                                                                    {t('Sr.')}
                                                                </th>
                                                                <th scope="col">
                                                                    {t('Contact Name')}
                                                                </th>
                                                                <th scope="col">
                                                                    {t('Contact Email')}

                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {unopeners.length ? (
                                                                unopeners.map(
                                                                    (
                                                                        contact,
                                                                        index
                                                                    ) => (
                                                                        <tr>
                                                                            <td>
                                                                                {(pageNumber3 -
                                                                                    1) *
                                                                                    perPage +
                                                                                    index +
                                                                                    1}
                                                                            </td>
                                                                            <td className="text-capitalize">
                                                                                {contact.first_name +
                                                                                    " " +
                                                                                    contact.last_name}
                                                                            </td>
                                                                            <td>
                                                                                {
                                                                                    contact.email
                                                                                }
                                                                            </td>
                                                                        </tr>
                                                                    )
                                                                )
                                                            ) : (
                                                                <tr>
                                                                    <td
                                                                        className="text-center"
                                                                        colSpan="3"
                                                                    >
                                                                        {t('No Contacts Found')}

                                                                    </td>
                                                                </tr>
                                                            )}
                                                        </tbody>
                                                    </table>
                                                    {unopeners.length ? (
                                                        <>
                                                            {/* pagination starts here */}
                                                            <div className="mt-2">
                                                                <Pagination
                                                                    activePage={
                                                                        pageNumber3
                                                                    }
                                                                    itemsCountPerPage={
                                                                        perPage
                                                                    }
                                                                    totalItemsCount={
                                                                        totalItems3
                                                                    }
                                                                    pageRangeDisplayed={
                                                                        pageRange
                                                                    }
                                                                    onChange={(
                                                                        e
                                                                    ) => {
                                                                        setPageNumber(
                                                                            e
                                                                        );
                                                                        setPageNumber3(
                                                                            e
                                                                        );
                                                                        setModule(
                                                                            3
                                                                        );
                                                                        setTerm(
                                                                            ""
                                                                        );
                                                                        setRefresh(
                                                                            !refresh
                                                                        );
                                                                    }}
                                                                />
                                                            </div>
                                                            {/* pagination ends here */}
                                                        </>
                                                    ) : (
                                                        ""
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="tab-pane fade"
                                        id="Clicked"
                                        role="tabpanel"
                                        aria-labelledby="contact-tab"
                                    >
                                        <div className="icons-container ">
                                            <div className="icons-content d-flex justify-content-between">
                                                <div className="icons-content-left d-flex">
                                                    <div className="IconFeature icon">
                                                        <img
                                                            src={IconFeature}
                                                            className="img-fluid"
                                                            alt="Download Contacts"
                                                            onClick={() => {
                                                                getReportCSV();
                                                                setRefresh(
                                                                    !refresh
                                                                );
                                                            }}
                                                        />
                                                    </div>
                                                </div>
                                                <div className="icons-content-right d-flex">
                                                    <div
                                                        className={
                                                            showSearchBar == 0
                                                                ? "icon-search icon"
                                                                : "d-none"
                                                        }
                                                        style={{
                                                            transition: "0.3s",
                                                        }}
                                                    >
                                                        <img
                                                            src={Group49}
                                                            className="img-fluid"
                                                            alt=""
                                                            onClick={() =>
                                                                setShowSearchBar(
                                                                    1
                                                                )
                                                            }
                                                        />
                                                    </div>
                                                    <div
                                                        id="toggle-search-bar"
                                                        className={
                                                            showSearchBar
                                                                ? "d-flex"
                                                                : "d-none"
                                                        }
                                                    >
                                                        <input
                                                            class="form-control me-2"
                                                            type="search"
                                                            placeholder={t("Search email address")}
                                                            id="search5"
                                                            aria-label="Search"
                                                        />
                                                        <button
                                                            class="search-button-btn"
                                                            onClick={() => {
                                                                setTerm(
                                                                    document.getElementById(
                                                                        "search5"
                                                                    ).value
                                                                );
                                                                setRefresh(
                                                                    !refresh
                                                                );
                                                            }}
                                                        >
                                                            {t('Search')}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="recipient-activities-table">
                                            <div className="row">
                                                <div className={showContactOpenDetails ? "col-md-6 table-responsive" : "col-md-12 table-responsive"}>
                                                    <table className="em-table align-middle table">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">
                                                                    {t('Sr.')}
                                                                </th>
                                                                <th scope="col">
                                                                    {t('Contact Email')}

                                                                </th>
                                                                <th scope="col" className="text-start">
                                                                    {t('Total Clicks')}
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {clicks.length ? (
                                                                clicks.map(
                                                                    (
                                                                        row,
                                                                        index
                                                                    ) => (
                                                                        <tr
                                                                            className={
                                                                                showingContactDetailsFor.id ==
                                                                                    row
                                                                                        .contact
                                                                                        .id
                                                                                    ? "table-success"
                                                                                    : ""
                                                                            }
                                                                            style={{
                                                                                cursor: "pointer",
                                                                            }}
                                                                            onClick={() => {
                                                                                setShowContactOpenDetails(
                                                                                    1
                                                                                );
                                                                                setOpenLogs(
                                                                                    JSON.parse(
                                                                                        row.clicks
                                                                                    )
                                                                                );
                                                                                setShowingContactDetailsFor(
                                                                                    row.contact
                                                                                );
                                                                            }}
                                                                        >
                                                                            <td>
                                                                                {(pageNumber5 -
                                                                                    1) *
                                                                                    perPage +
                                                                                    index +
                                                                                    1}
                                                                            </td>
                                                                            <td className="text-capitalize">
                                                                                {
                                                                                    row
                                                                                        .contact
                                                                                        .email
                                                                                }
                                                                            </td>
                                                                            <td>
                                                                                {
                                                                                    JSON.parse(
                                                                                        row.clicks
                                                                                    )
                                                                                        .length
                                                                                }{" "}
                                                                                Clicks
                                                                            </td>
                                                                            <td className="d-none">
                                                                                {JSON.parse(
                                                                                    row.clicks
                                                                                ).map(
                                                                                    (
                                                                                        log
                                                                                    ) => {
                                                                                        return (
                                                                                            <Moment format="DD MMMM YYYY, h:mm:ss a">
                                                                                                {moment.tz(moment(log).utc(), localStorage.timezone)}
                                                                                            </Moment>
                                                                                        );
                                                                                    }
                                                                                )}
                                                                            </td>
                                                                        </tr>
                                                                    )
                                                                )
                                                            ) : (
                                                                <tr>
                                                                    <td
                                                                        className="text-center"
                                                                        colSpan="3"
                                                                    >
                                                                        {t('No Contacts Found')}

                                                                    </td>
                                                                </tr>
                                                            )}
                                                        </tbody>
                                                    </table>
                                                    {clicks.length ? (
                                                        <>
                                                            {/* pagination starts here */}
                                                            <div className="mt-2">
                                                                <Pagination
                                                                    activePage={
                                                                        pageNumber5
                                                                    }
                                                                    itemsCountPerPage={
                                                                        perPage
                                                                    }
                                                                    totalItemsCount={
                                                                        totalItems5
                                                                    }
                                                                    pageRangeDisplayed={
                                                                        pageRange
                                                                    }
                                                                    onChange={(
                                                                        e
                                                                    ) => {
                                                                        setPageNumber(
                                                                            e
                                                                        );
                                                                        setPageNumber5(
                                                                            e
                                                                        );
                                                                        setModule(
                                                                            5
                                                                        );
                                                                        setTerm(
                                                                            ""
                                                                        );
                                                                        setRefresh(
                                                                            !refresh
                                                                        );
                                                                    }}
                                                                />
                                                            </div>
                                                            {/* pagination ends here */}
                                                        </>
                                                    ) : (
                                                        ""
                                                    )}
                                                </div>

                                                <div
                                                    className={
                                                        showContactOpenDetails
                                                            ? "col-md-6 recipient-activities-table-pagination"
                                                            : "d-none"
                                                    }
                                                >
                                                    <table className="table active-user-table-content">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">
                                                                    {t('Clicked by')} -{" "}
                                                                    {
                                                                        showingContactDetailsFor.email
                                                                    }
                                                                </th>
                                                                <th scope="col">
                                                                    <img
                                                                        src={
                                                                            Union5
                                                                        }
                                                                        alt="Close"
                                                                        onClick={() => {
                                                                            setShowContactOpenDetails(
                                                                                0
                                                                            );
                                                                            setShowingContactDetailsFor(
                                                                                []
                                                                            );
                                                                        }}
                                                                        className="img-fluid"
                                                                        style={{
                                                                            cursor: "pointer",
                                                                        }}
                                                                    />
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {openLogs.map(
                                                                (log) => (
                                                                    <p>
                                                                        <Moment format="DD MMMM YYYY, h:mm:ss a">
                                                                            {moment.tz(moment(log).utc(), localStorage.timezone)}
                                                                        </Moment>
                                                                    </p>
                                                                )
                                                            )}
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="tab-pane fade"
                                        id="Unsubscribes"
                                        role="tabpanel"
                                        aria-labelledby="contact-tab"
                                    >
                                        <div className="icons-container">
                                            <div className="icons-content d-flex justify-content-between">
                                                <div className="icons-content-left d-flex">
                                                    <div className="IconFeature icon">
                                                        <img
                                                            src={IconFeature}
                                                            className="img-fluid"
                                                            alt="Download Contacts"
                                                            onClick={() => {
                                                                getReportCSV();
                                                                setRefresh(
                                                                    !refresh
                                                                );
                                                            }}
                                                        />
                                                    </div>
                                                </div>
                                                <div className="icons-content-right d-flex">
                                                    <div
                                                        className={
                                                            showSearchBar == 0
                                                                ? "icon-search icon"
                                                                : "d-none"
                                                        }
                                                        style={{
                                                            transition: "0.3s",
                                                        }}
                                                    >
                                                        <img
                                                            src={Group49}
                                                            className="img-fluid"
                                                            alt=""
                                                            onClick={() =>
                                                                setShowSearchBar(
                                                                    1
                                                                )
                                                            }
                                                        />
                                                    </div>
                                                    <div
                                                        id="toggle-search-bar"
                                                        className={
                                                            showSearchBar
                                                                ? "d-flex"
                                                                : "d-none"
                                                        }
                                                    >
                                                        <input
                                                            class="form-control me-2"
                                                            type="search"
                                                            placeholder={t("Search email address")}
                                                            id="search6"
                                                            aria-label="Search"
                                                        />
                                                        <button
                                                            class="search-button-btn"
                                                            onClick={() => {
                                                                setTerm(
                                                                    document.getElementById(
                                                                        "search6"
                                                                    ).value
                                                                );
                                                                setRefresh(
                                                                    !refresh
                                                                );
                                                            }}
                                                        >
                                                            {t('Search')}
                                                        </button>
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
                                                                <th scope="col">
                                                                    {t('Sr.')}
                                                                </th>
                                                                <th scope="col">
                                                                    {t('Contact Name')}
                                                                </th>
                                                                <th scope="col">
                                                                    {t('Contact Email')}

                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {unSubscribers.length ? (
                                                                unSubscribers.map(
                                                                    (
                                                                        contact,
                                                                        index
                                                                    ) => (
                                                                        <tr>
                                                                            <td>
                                                                                {(pageNumber6 -
                                                                                    1) *
                                                                                    perPage +
                                                                                    index +
                                                                                    1}
                                                                            </td>
                                                                            <td className="text-capitalize">
                                                                                {contact.first_name +
                                                                                    " " +
                                                                                    contact.last_name}
                                                                            </td>
                                                                            <td>
                                                                                {
                                                                                    contact.email
                                                                                }
                                                                            </td>
                                                                        </tr>
                                                                    )
                                                                )
                                                            ) : (
                                                                <tr>
                                                                    <td
                                                                        className="text-center"
                                                                        colSpan="3"
                                                                    >
                                                                        {t('No Contacts Found')}

                                                                    </td>
                                                                </tr>
                                                            )}
                                                        </tbody>
                                                    </table>
                                                    {unSubscribers.length ? (
                                                        <>
                                                            {/* pagination starts here */}
                                                            <div className="mt-2">
                                                                <Pagination
                                                                    activePage={
                                                                        pageNumber6
                                                                    }
                                                                    itemsCountPerPage={
                                                                        perPage
                                                                    }
                                                                    totalItemsCount={
                                                                        totalItems6
                                                                    }
                                                                    pageRangeDisplayed={
                                                                        pageRange
                                                                    }
                                                                    onChange={(
                                                                        e
                                                                    ) => {
                                                                        setPageNumber(
                                                                            e
                                                                        );
                                                                        setPageNumber6(
                                                                            e
                                                                        );
                                                                        setModule(
                                                                            6
                                                                        );
                                                                        setTerm(
                                                                            ""
                                                                        );
                                                                        setRefresh(
                                                                            !refresh
                                                                        );
                                                                    }}
                                                                />
                                                            </div>
                                                            {/* pagination ends here */}
                                                        </>
                                                    ) : (
                                                        ""
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            className="tab-pane fade"
                            id="nav-contact"
                            role="tabpanel"
                            aria-labelledby="nav-contact-tab"
                        >
                            <div className="icons-container">
                                <div className="icons-content d-flex justify-content-between">
                                    <div className="icons-content-left d-flex">
                                        <div className="IconFeature icon">
                                            <img src={IconFeature} className="img-fluid" alt="Download Contacts" onClick={() => { getReportCSV(); setRefresh(!refresh) }} />
                                        </div>
                                    </div>
                                    <div className="icons-content-right d-flex ">
                                        <div
                                            className={
                                                showSearchBar == 0
                                                    ? "icon-search icon"
                                                    : "d-none"
                                            }
                                            style={{ transition: "0.3s" }}
                                        >
                                            <img
                                                src={Group49}
                                                className="img-fluid"
                                                alt=""
                                                onClick={() =>
                                                    setShowSearchBar(1)
                                                }
                                            />
                                        </div>
                                        <div
                                            id="toggle-search-bar"
                                            className={
                                                showSearchBar
                                                    ? "d-flex"
                                                    : "d-none"
                                            }
                                        >
                                            <input
                                                class="form-control me-2"
                                                type="search"
                                                placeholder={t("Search email address")}
                                                id="search7"
                                                aria-label="Search"
                                            />
                                            <button
                                                class="search-button-btn"
                                                onClick={() => {
                                                    setTerm(
                                                        document.getElementById(
                                                            "search7"
                                                        ).value
                                                    );
                                                    setRefresh(!refresh);
                                                }}
                                            >
                                                {t('Search')}
                                            </button>
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
                                                    <th scope="col">{t('Sr.')}</th>
                                                    <th scope="col">
                                                        {t('Contact Name')}
                                                    </th>
                                                    <th scope="col">
                                                        {t('Contact Email')}
                                                    </th>
                                                    <th scope="col">
                                                        {t('Clicked Link')}
                                                    </th>
                                                    <th scope="col">
                                                        {t('Clicked At')}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {clickLogs.length ? (
                                                    clickLogs.map(
                                                        (row, index) => (
                                                            <tr>
                                                                <td>
                                                                    {(pageNumber7 -
                                                                        1) *
                                                                        perPage +
                                                                        index +
                                                                        1}
                                                                </td>
                                                                <td className="text-capitalize">
                                                                    {row.contact
                                                                        .first_name +
                                                                        " " +
                                                                        row
                                                                            .contact
                                                                            .last_name}
                                                                </td>
                                                                <td>
                                                                    {
                                                                        row
                                                                            .contact
                                                                            .email
                                                                    }
                                                                </td>
                                                                <td>
                                                                    {row.link}
                                                                </td>
                                                                <td>
                                                                    <Moment format="DD MMMM YYYY, h:mm:ss a">
                                                                        {moment.tz(moment(row.created_at).utc(), localStorage.timezone)}
                                                                    </Moment>
                                                                </td>
                                                            </tr>
                                                        )
                                                    )
                                                ) : (
                                                    <tr>
                                                        <td
                                                            className="text-center"
                                                            colSpan="5"
                                                        >
                                                            {t('No Contacts Found')}
                                                        </td>
                                                    </tr>
                                                )}
                                            </tbody>
                                        </table>
                                        {clickLogs.length ? (
                                            <>
                                                {/* pagination starts here */}
                                                <div className="mt-2">
                                                    <Pagination
                                                        activePage={pageNumber7}
                                                        itemsCountPerPage={
                                                            perPage
                                                        }
                                                        totalItemsCount={
                                                            totalItems7
                                                        }
                                                        pageRangeDisplayed={
                                                            pageRange
                                                        }
                                                        onChange={(e) => {
                                                            setPageNumber(e);
                                                            setPageNumber7(e);
                                                            setModule(7);
                                                            setTerm("");
                                                            setRefresh(
                                                                !refresh
                                                            );
                                                        }}
                                                    />
                                                </div>
                                                {/* pagination ends here */}
                                            </>
                                        ) : (
                                            ""
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            className="tab-pane fade"
                            id="nav-bounce"
                            role="tabpanel"
                            aria-labelledby="nav-bounce-tab"
                        >
                            <div className="icons-container">
                                <div className="icons-content d-flex justify-content-between">
                                    <div className="icons-content-left d-flex">
                                        <div className="IconFeature icon">
                                            <img
                                                src={IconFeature}
                                                className="img-fluid"
                                                alt="Download Contacts"
                                                onClick={() => {
                                                    getReportCSV();
                                                    setRefresh(!refresh);
                                                }}
                                            />
                                        </div>
                                    </div>
                                    <div className="icons-content-right d-flex">
                                        <div
                                            className={
                                                showSearchBar == 0
                                                    ? "icon-search icon"
                                                    : "d-none"
                                            }
                                            style={{ transition: "0.3s" }}
                                        >
                                            <img
                                                src={Group49}
                                                className="img-fluid"
                                                alt=""
                                                onClick={() =>
                                                    setShowSearchBar(1)
                                                }
                                            />
                                        </div>
                                        <div
                                            id="toggle-search-bar"
                                            className={
                                                showSearchBar
                                                    ? "d-flex"
                                                    : "d-none"
                                            }
                                        >
                                            <input
                                                class="form-control me-2"
                                                type="search"
                                                placeholder={t("Search email address")}
                                                id="search8"
                                                aria-label="Search"
                                            />
                                            <button
                                                class="search-button-btn"
                                                onClick={() => {
                                                    setTerm(
                                                        document.getElementById(
                                                            "search8"
                                                        ).value
                                                    );
                                                    setRefresh(!refresh);
                                                }}
                                            >
                                                {t('Search')}
                                            </button>
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
                                                    <th scope="col">{t('Sr.')}</th>
                                                    <th scope="col">
                                                        {t('Contact Name')}
                                                    </th>
                                                    <th scope="col">
                                                        {t('Contact Email')}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {bounces.length ? (
                                                    bounces.map(
                                                        (contact, index) => (
                                                            <tr>
                                                                <td>
                                                                    {(pageNumber8 -
                                                                        1) *
                                                                        perPage +
                                                                        index +
                                                                        1}
                                                                </td>
                                                                <td className="text-capitalize">
                                                                    {contact.first_name +
                                                                        " " +
                                                                        contact.last_name}
                                                                </td>
                                                                <td>
                                                                    {
                                                                        contact.email
                                                                    }
                                                                </td>
                                                            </tr>
                                                        )
                                                    )
                                                ) : (
                                                    <tr>
                                                        <td
                                                            className="text-center"
                                                            colSpan="3"
                                                        >
                                                            {t('No Contacts Found')}
                                                        </td>
                                                    </tr>
                                                )}
                                            </tbody>
                                        </table>
                                        {bounces.length ? (
                                            <>
                                                {/* pagination starts here */}
                                                <div className="mt-2">
                                                    <Pagination
                                                        activePage={pageNumber8}
                                                        itemsCountPerPage={
                                                            perPage
                                                        }
                                                        totalItemsCount={
                                                            totalItems8
                                                        }
                                                        pageRangeDisplayed={
                                                            pageRange
                                                        }
                                                        onChange={(e) => {
                                                            setPageNumber(e);
                                                            setPageNumber8(e);
                                                            setModule(8);
                                                            setTerm("");
                                                            setRefresh(
                                                                !refresh
                                                            );
                                                        }}
                                                    />
                                                </div>
                                                {/* pagination ends here */}
                                            </>
                                        ) : (
                                            ""
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            className="tab-pane fade"
                            id="nav-split"
                            role="tabpanel"
                            aria-labelledby="nav-split-tab"
                        >
                            <div className="split-report-tab p-5">
                                <h3 className="mb-3 text-center">
                                    {t('Link Clicks')} & {t('Open Rate')}
                                </h3>
                                {/* Chart here */}
                                <ResponsiveContainer width="100%" height={300}>
                                    <BarChart
                                        width={730}
                                        height={250}
                                        data={splitData}
                                        barGap="40%"
                                        barSize="60%"
                                    >
                                        <CartesianGrid strokeDasharray="3 3" />
                                        <XAxis dataKey="name" />
                                        <YAxis />
                                        <Tooltip />
                                        <Legend />
                                        <Bar
                                            dataKey={t("Open Rate")}
                                            fill="#1D2579"
                                        />
                                        <Bar
                                            dataKey={t("Link Clicks")}
                                            fill="#24E096"
                                        />
                                    </BarChart>
                                </ResponsiveContainer>
                            </div>

                            <div className="recipient-activities-tabs split-tabs">
                                <div className="tabs-container">
                                    <ul
                                        class="nav nav-tabs justify-content-around"
                                        id="myTab"
                                        role="tablist"
                                        onClick={() => {
                                            setShowContactOpenDetails(0);
                                            setShowingContactDetailsFor([]);
                                        }}
                                    >
                                        <li
                                            class="nav-item"
                                            role="presentation"
                                        >
                                            <button
                                                class="nav-link active"
                                                id="one-tab"
                                                data-bs-toggle="tab"
                                                data-bs-target="#split1"
                                                type="button"
                                                role="tab"
                                                aria-controls="split1"
                                                aria-selected="true"
                                            >
                                                {" "}
                                                {byContent
                                                    ? all_sent_to.filter(
                                                        (contact) =>
                                                            contact.pivot
                                                                .content_id ==
                                                            split[0]
                                                    ).length
                                                    : all_sent_to.filter(
                                                        (contact) =>
                                                            contact.pivot
                                                                .subject_id ==
                                                            split[0]
                                                    ).length}{" "}
                                                <p>{t('Section 1')}</p>
                                            </button>
                                        </li>
                                        <li
                                            class="nav-item"
                                            role="presentation"
                                        >
                                            <button
                                                class="nav-link"
                                                id="two-tab"
                                                data-bs-toggle="tab"
                                                data-bs-target="#split2"
                                                type="button"
                                                role="tab"
                                                aria-controls="split2"
                                                aria-selected="false"
                                            >
                                                {" "}
                                                {byContent
                                                    ? all_sent_to.filter(
                                                        (contact) =>
                                                            contact.pivot
                                                                .content_id ==
                                                            split[1]
                                                    ).length
                                                    : all_sent_to.filter(
                                                        (contact) =>
                                                            contact.pivot
                                                                .subject_id ==
                                                            split[1]
                                                    ).length}{" "}
                                                <p>{t('Section 2')}</p>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tab-content" id="myTabContent">
                                    <div
                                        class="tab-pane fade show active"
                                        id="split1"
                                        role="tabpanel"
                                        aria-labelledby="one-tab"
                                    >
                                        <div className="recipient-activities-table">
                                            <div className="row">
                                                <div className="split-display my-3 text-center">
                                                    <div id="splitDisplay1" />
                                                </div>
                                                <div className="col-md-12 table-responsive">
                                                    <table className="em-table align-middle table">
                                                        <thead>
                                                            <tr>
                                                                <th>
                                                                    Contact Name
                                                                </th>
                                                                <th>Email</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {byContent ? (
                                                                all_sent_to.filter(
                                                                    (contact) =>
                                                                        contact
                                                                            .pivot
                                                                            .content_id ==
                                                                        split[0]
                                                                ).length ? (
                                                                    all_sent_to
                                                                        .filter(
                                                                            (
                                                                                contact
                                                                            ) =>
                                                                                contact
                                                                                    .pivot
                                                                                    .content_id ==
                                                                                split[0]
                                                                        )
                                                                        .map(
                                                                            (
                                                                                contact,
                                                                                index
                                                                            ) => (
                                                                                <tr
                                                                                    key={
                                                                                        contact.id
                                                                                    }
                                                                                >
                                                                                    <td className="text-capitalize">
                                                                                        {contact.first_name +
                                                                                            " " +
                                                                                            contact.last_name}
                                                                                    </td>
                                                                                    <td>
                                                                                        {
                                                                                            contact.email
                                                                                        }
                                                                                    </td>
                                                                                    <td>
                                                                                        <Badge
                                                                                            className={
                                                                                                "d-inline-block align-top badge bg-" +
                                                                                                (contact
                                                                                                    .pivot
                                                                                                    .failed_at
                                                                                                    ? "danger"
                                                                                                    : contact
                                                                                                        .pivot
                                                                                                        .bounced_at
                                                                                                        ? "info"
                                                                                                        : "success")
                                                                                            }
                                                                                        >
                                                                                            {" "}
                                                                                            {contact
                                                                                                .pivot
                                                                                                .failed_at
                                                                                                ? "Failed"
                                                                                                : contact
                                                                                                    .pivot
                                                                                                    .bounced_at
                                                                                                    ? "Bounced"
                                                                                                    : "Sent"}{" "}
                                                                                        </Badge>
                                                                                    </td>
                                                                                </tr>
                                                                            )
                                                                        )
                                                                ) : (
                                                                    <tr>
                                                                        <td
                                                                            className="text-center"
                                                                            colSpan="3"
                                                                        >
                                                                            No
                                                                            Contacts
                                                                            Found
                                                                        </td>
                                                                    </tr>
                                                                )
                                                            ) : all_sent_to.filter(
                                                                (contact) =>
                                                                    contact
                                                                        .pivot
                                                                        .subject_id ==
                                                                    split[0]
                                                            ).length ? (
                                                                all_sent_to
                                                                    .filter(
                                                                        (
                                                                            contact
                                                                        ) =>
                                                                            contact
                                                                                .pivot
                                                                                .subject_id ==
                                                                            split[0]
                                                                    )
                                                                    .map(
                                                                        (
                                                                            contact,
                                                                            index
                                                                        ) => (
                                                                            <tr
                                                                                key={
                                                                                    contact.id
                                                                                }
                                                                            >
                                                                                <td className="text-capitalize">
                                                                                    {contact.first_name +
                                                                                        " " +
                                                                                        contact.last_name}
                                                                                </td>
                                                                                <td>
                                                                                    {
                                                                                        contact.email
                                                                                    }
                                                                                </td>
                                                                                <td>
                                                                                    <Badge
                                                                                        className={
                                                                                            "d-inline-block align-top badge bg-" +
                                                                                            (contact
                                                                                                .pivot
                                                                                                .failed_at
                                                                                                ? "danger"
                                                                                                : contact
                                                                                                    .pivot
                                                                                                    .bounced_at
                                                                                                    ? "info"
                                                                                                    : "success")
                                                                                        }
                                                                                    >
                                                                                        {" "}
                                                                                        {contact
                                                                                            .pivot
                                                                                            .failed_at
                                                                                            ? "Failed"
                                                                                            : contact
                                                                                                .pivot
                                                                                                .bounced_at
                                                                                                ? "Bounced"
                                                                                                : "Sent"}{" "}
                                                                                    </Badge>
                                                                                </td>
                                                                            </tr>
                                                                        )
                                                                    )
                                                            ) : (
                                                                <tr>
                                                                    <td
                                                                        className="text-center"
                                                                        colSpan="5"
                                                                    >
                                                                        No
                                                                        Contacts
                                                                        Found
                                                                    </td>
                                                                </tr>
                                                            )}
                                                        </tbody>
                                                    </table>
                                                    {/* pagination */}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="tab-pane fade"
                                        id="split2"
                                        role="tabpanel"
                                        aria-labelledby="two-tab"
                                    >
                                        <div className="recipient-activities-table">
                                            <div className="row">
                                                <div className="split-display my-3 text-center">
                                                    <div id="splitDisplay2" />
                                                </div>
                                                <div className="col-md-12 table-responsive">
                                                    <table className="em-table align-middle table">
                                                        <thead>
                                                            <tr>
                                                                <th>
                                                                    Contact Name
                                                                </th>
                                                                <th>Email</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {byContent ? (
                                                                all_sent_to.filter(
                                                                    (contact) =>
                                                                        contact
                                                                            .pivot
                                                                            .content_id ==
                                                                        split[1]
                                                                ).length ? (
                                                                    all_sent_to
                                                                        .filter(
                                                                            (
                                                                                contact
                                                                            ) =>
                                                                                contact
                                                                                    .pivot
                                                                                    .content_id ==
                                                                                split[1]
                                                                        )
                                                                        .map(
                                                                            (
                                                                                contact,
                                                                                index
                                                                            ) => (
                                                                                <tr
                                                                                    key={
                                                                                        contact.id
                                                                                    }
                                                                                >
                                                                                    <td className="text-capitalize">
                                                                                        {contact.first_name +
                                                                                            " " +
                                                                                            contact.last_name}
                                                                                    </td>
                                                                                    <td>
                                                                                        {
                                                                                            contact.email
                                                                                        }
                                                                                    </td>
                                                                                    <td>
                                                                                        <Badge
                                                                                            className={
                                                                                                "d-inline-block align-top badge bg-" +
                                                                                                (contact
                                                                                                    .pivot
                                                                                                    .failed_at
                                                                                                    ? "danger"
                                                                                                    : contact
                                                                                                        .pivot
                                                                                                        .bounced_at
                                                                                                        ? "info"
                                                                                                        : "success")
                                                                                            }
                                                                                        >
                                                                                            {" "}
                                                                                            {contact
                                                                                                .pivot
                                                                                                .failed_at
                                                                                                ? "Failed"
                                                                                                : contact
                                                                                                    .pivot
                                                                                                    .bounced_at
                                                                                                    ? "Bounced"
                                                                                                    : "Sent"}{" "}
                                                                                        </Badge>
                                                                                    </td>
                                                                                </tr>
                                                                            )
                                                                        )
                                                                ) : (
                                                                    <tr>
                                                                        <td
                                                                            className="text-center"
                                                                            colSpan="5"
                                                                        >
                                                                            No
                                                                            Contacts
                                                                            Found
                                                                        </td>
                                                                    </tr>
                                                                )
                                                            ) : all_sent_to.filter(
                                                                (contact) =>
                                                                    contact
                                                                        .pivot
                                                                        .subject_id ==
                                                                    split[1]
                                                            ).length ? (
                                                                all_sent_to
                                                                    .filter(
                                                                        (
                                                                            contact
                                                                        ) =>
                                                                            contact
                                                                                .pivot
                                                                                .subject_id ==
                                                                            split[1]
                                                                    )
                                                                    .map(
                                                                        (
                                                                            contact,
                                                                            index
                                                                        ) => (
                                                                            <tr
                                                                                key={
                                                                                    contact.id
                                                                                }
                                                                            >
                                                                                <td className="text-capitalize">
                                                                                    {contact.first_name +
                                                                                        " " +
                                                                                        contact.last_name}
                                                                                </td>
                                                                                <td>
                                                                                    {
                                                                                        contact.email
                                                                                    }
                                                                                </td>
                                                                                <td>
                                                                                    <Badge
                                                                                        className={
                                                                                            "d-inline-block align-top badge bg-" +
                                                                                            (contact
                                                                                                .pivot
                                                                                                .failed_at
                                                                                                ? "danger"
                                                                                                : contact
                                                                                                    .pivot
                                                                                                    .bounced_at
                                                                                                    ? "info"
                                                                                                    : "success")
                                                                                        }
                                                                                    >
                                                                                        {" "}
                                                                                        {contact
                                                                                            .pivot
                                                                                            .failed_at
                                                                                            ? "Failed"
                                                                                            : contact
                                                                                                .pivot
                                                                                                .bounced_at
                                                                                                ? "Bounced"
                                                                                                : "Sent"}{" "}
                                                                                    </Badge>
                                                                                </td>
                                                                            </tr>
                                                                        )
                                                                    )
                                                            ) : (
                                                                <tr>
                                                                    <td
                                                                        className="text-center"
                                                                        colSpan="5"
                                                                    >
                                                                        {t('No Contacts Found')}

                                                                    </td>
                                                                </tr>
                                                            )}
                                                        </tbody>
                                                    </table>
                                                    {/* pagination */}
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
                <button
                    className="btn btn-secondary ms-3 mb-3"
                    onClick={() => props.back()}
                >
                    <span>{t('Back')}</span>
                </button>
            </div>
        </>
    );
};

export default withTranslation()(ReportHistory);
