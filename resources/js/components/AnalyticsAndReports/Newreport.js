import React, { useEffect, useState, useRef } from "react";
import { Link, useHistory } from "react-router-dom";
import {
    Container,
    Form,
    Row,
    Col,
    Button,
    Modal,
    Table,
} from "react-bootstrap";
import Group32 from "../../assets/images/Group 32.svg";
import Subtraction from "../../assets/images/Subtraction 1.svg";
import Mine from "../../assets/images/mine.svg";
import Feather from "../../assets/images/Icon feather-map-pin.svg";
import Group37 from "../../assets/images/Group 37.svg";
import Sender from "../../assets/images/Image4.png";
import Group48 from "../../assets/images/Group 48.svg";
import IconFeature from "../../assets/images/Icon feather-download.svg";
import IconMaterial from "../../assets/images/Icon material-email.svg";
import Path3970 from "../../assets/images/Path 3970.svg";
import Path3971 from "../../assets/images/Path 3971.svg";
import Union5 from "../../assets/images/Union 5.svg";
import Union4 from "../../assets/images/Union 4.svg";
import Path3972 from "../../assets/images/Path 3972.svg";
import Group49 from "../../assets/images/Group 49.svg";
import SearchIcon from "../../assets/images/search.svg";
import { withTranslation } from 'react-i18next';
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
} from "recharts";

const Newreport = (props) => {
    const { t } = props;
    const data = [
        {
            name: "Page A",
            uv: 4000,
            pv: 2400,
            amt: 2400,
        },
        {
            name: "Page B",
            uv: 3000,
            pv: 1398,
            amt: 2210,
        },
        {
            name: "Page C",
            uv: 2000,
            pv: 9800,
            amt: 2290,
        },
        {
            name: "Page D",
            uv: 2780,
            pv: 3908,
            amt: 2000,
        },
        {
            name: "Page E",
            uv: 1890,
            pv: 4800,
            amt: 2181,
        },
        {
            name: "Page F",
            uv: 2390,
            pv: 3800,
            amt: 2500,
        },
        {
            name: "Page G",
            uv: 3490,
            pv: 4300,
            amt: 2100,
        },
    ];
    const data01 = [
        { name: "Group A", value: 400 },
        { name: "Group B", value: 300 },
        { name: "Group C", value: 300 },
        { name: "Group D", value: 200 },
    ];

    const [locationMap, setLocationMap] = useState(1);
    const [showSearchBar, setShowSearchBar] = useState(0);

    return (
        <>
            <section className="reports">
                <h2 >{t('Campaign Name')}:</h2>
                <div className="about-reports">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button
                                class="nav-link active"
                                id="nav-home-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#nav-home"
                                type="button"
                                role="tab"
                                aria-controls="nav-home"
                                aria-selected="true"
                            >
                                {t('Report Summary')}
                            </button>
                            <button
                                class="nav-link"
                                id="nav-profile-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#nav-profile"
                                type="button"
                                role="tab"
                                aria-controls="nav-profile"
                                aria-selected="false"
                            >
                                {t('Recipient Activities')}
                            </button>
                            <button
                                class="nav-link"
                                id="nav-contact-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#nav-contact"
                                type="button"
                                role="tab"
                                aria-controls="nav-contact"
                                aria-selected="false"
                            >
                                {t('Click Activities')}
                            </button>
                            <button
                                class="nav-link"
                                id="nav-bounce-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#nav-bounce"
                                type="button"
                                role="tab"
                                aria-controls="nav-bounce"
                                aria-selected="false"
                            >
                                {t('Bounces and Auto-replies')}
                            </button>
                            <button
                                class="nav-link"
                                id="nav-stat-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#nav-stat"
                                type="button"
                                role="tab"
                                aria-controls="nav-stat"
                                aria-selected="false"
                            >
                                {t('Social stats')}
                            </button>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div
                            class="tab-pane fade show active"
                            id="nav-home"
                            role="tabpanel"
                            aria-labelledby="nav-home-tab"
                        >
                            <div className="row">
                                <div className="col-md-7 abt-rt">
                                    <div className="real-time">
                                        <h5>Real-Time Campaign Data</h5>
                                    </div>
                                    <div className="rt-data">
                                        <h6>20</h6>
                                        <p>Total Emails Sent</p>
                                        <span>Sep 16, 2021 06:49PM PKT</span>
                                    </div>
                                    <div className="rt-progress">
                                        <div class="progress">
                                            <div
                                                class="progress-bar bg-success"
                                                role="progressbar"
                                                style={{
                                                    width: "90%",
                                                    ariaValuemax: "100",
                                                }}
                                            ></div>
                                        </div>
                                        <span>100.0% Delivered</span>
                                    </div>
                                    <div className="rt-dlvr">
                                        <div className="dlvr">
                                            <div className="delivered"></div>
                                            <div className="rt-desc">
                                                <p>Delivered 100.0%</p>
                                                <p>20 Contacts</p>
                                            </div>
                                        </div>
                                        <div className="dlvr">
                                            <div className="bounced"></div>
                                            <div className="rt-desc">
                                                <p>Bounces 0.0%</p>
                                                <p>0 Contacts</p>
                                            </div>
                                        </div>
                                        <div className="dlvr">
                                            <div className="unsent"></div>
                                            <div className="rt-desc">
                                                <p>Unsent 0.0%</p>
                                                <p>0 Contacts</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="rt-prog">
                                        <div class="progress">
                                            <div
                                                class="progress-bar bg-success"
                                                role="progressbar"
                                                style={{
                                                    width: "90%",
                                                    ariaValuemax: "100",
                                                }}
                                            ></div>
                                        </div>
                                        <span>[55.0% + 45.0%]</span>
                                    </div>
                                    <div className="rt-dlvr">
                                        <div className="dlvr">
                                            <div className="open"></div>
                                            <div className="rt-desc">
                                                <p>Unique Opens 55.0%</p>
                                                <p>11 Contacts</p>
                                            </div>
                                        </div>
                                        <div className="dlvr">
                                            <div className="clicks"></div>
                                            <div className="rt-desc">
                                                <p>Unique Clickes 10.0%</p>
                                                <p>2 Contacts</p>
                                            </div>
                                        </div>
                                        <div className="dlvr">
                                            <div className="unopen"></div>
                                            <div className="rt-desc">
                                                <p>Unopened 45.0%</p>
                                                <p>9 Contacts</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="rt-dlvr">
                                        <div className="dlvr">
                                            <div className="open"></div>
                                            <div className="rt-desc">
                                                <p>Clicks/Open R…18.2%</p>
                                            </div>
                                        </div>
                                        <div className="dlvr">
                                            <div className="click">
                                                <img src={Group32} />
                                            </div>
                                            <div className="rt-desc">
                                                <p>Unsubscribes 0.0%</p>
                                                <p>0 Contacts</p>
                                            </div>
                                        </div>
                                        <div className="dlvr">
                                            <div className="click">
                                                <img src={Group32} />
                                            </div>
                                            <div className="rt-desc">
                                                <p>Complaints 0.0%</p>
                                                <p>0 Contacts</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-md-5 abt-rt">
                                    <div className="real-time">
                                        <h5>Campaign Reach</h5>
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
                                                    fill="#82ca9d"
                                                    label
                                                />
                                            </PieChart>
                                        </ResponsiveContainer>
                                        <div className="reach-desc">
                                            <p>11</p>
                                            <p>Total Reach</p>
                                        </div>
                                    </div>
                                    <div class="abt-email-container">
                                        <div className="abt-email">
                                            <div className="about-mail">
                                                <div className="abt-mail"> </div>
                                                <p>Email</p>
                                            </div>
                                            <div className="abt-views">
                                                <p>11 Views</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="share-views">
                                        <p>
                                            Sharing this campaign on social
                                            media can help you increase your
                                            reach
                                        </p>
                                        <a href="#">Share Now</a>
                                    </div>
                                </div>
                            </div>
                            <div className="row">
                                <div className="campaign-graph-holder reach-graph">
                                    <h5>Opens by Time</h5>
                                    <ResponsiveContainer
                                        width="100%"
                                        height={200}
                                    >
                                        <LineChart
                                            width={500}
                                            height={300}
                                            data={data}
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
                                                dataKey="pv"
                                                stroke="#8884d8"
                                                activeDot={{ r: 8 }}
                                            />
                                            <Line
                                                type="monotone"
                                                dataKey="uv"
                                                stroke="#82ca9d"
                                            />
                                        </LineChart>
                                    </ResponsiveContainer>
                                </div>
                            </div>
                            <div className="row reach-graph">
                                <div className="map">
                                    <div className="about-map">
                                        <h5>Opens by Location</h5>
                                    </div>
                                    <div className="map-button">
                                        <div
                                            className="feather active"
                                            onClick={() => setLocationMap(1)}
                                        >
                                            <img src={Feather} />
                                        </div>
                                        <div
                                            className="mine active"
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
                            </div>
                            <div className="row reach-graph">
                                <h5>Subject and Sender details</h5>
                                <div className="col-md-9 sender-details">
                                    <div className="row">
                                        <div className="col-md-4">
                                            <div className="sender-image">
                                                <img
                                                    className="img-fluid"
                                                    src={Sender}
                                                />
                                            </div>
                                        </div>
                                        <div className="col-md-3">
                                            <div className="about-sender">
                                                <ul>
                                                    <li>Subject</li>
                                                    <li>Sender Name</li>
                                                    <li>Sender Address</li>
                                                    <li>Reply Tracking</li>
                                                    <li>Reply-to Address</li>
                                                    <li>Content Type</li>
                                                    <li>Created on</li>
                                                    <li>
                                                        Recipient Personalized
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div className="col-md-5">
                                            <div className="about-sender abt-send">
                                                <ul>
                                                    <li>
                                                        : Mathletes! Get Ready
                                                        for the New Challenge
                                                    </li>
                                                    <li>: Mathlete PK</li>
                                                    <li>
                                                        :
                                                        mathlete.online@gmail.com
                                                    </li>
                                                    <li>: Disable</li>
                                                    <li>
                                                        :
                                                        Mathlete.online@gmail.com
                                                    </li>
                                                    <li>
                                                        : Sep 16, 2021 12:55 PM
                                                    </li>
                                                    <li>: Yes</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="tab-pane fade"
                            id="nav-profile"
                            role="tabpanel"
                            aria-labelledby="nav-profile-tab"
                        >
                            <div className="recipient-activities-tabs">
                                <div className="tabs-container">
                                    <ul
                                        class="nav nav-tabs"
                                        id="myTab"
                                        role="tablist"
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
                                            >
                                                20<p>Sent</p>
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
                                            >
                                                20<p>Delivered</p>
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
                                            >
                                                11<p>Opened</p>
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
                                            >
                                                9<p>Unopened</p>
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
                                            >
                                                2<p>Clicked</p>
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
                                                data-bs-target="#Replied"
                                                type="button"
                                                role="tab"
                                                aria-controls="Replied"
                                                aria-selected="false"
                                            >
                                                4<p>Replied</p>
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
                                            >
                                                0<p>Unsubscribes</p>
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
                                                data-bs-target="#Marked"
                                                type="button"
                                                role="tab"
                                                aria-controls="Marked"
                                                aria-selected="false"
                                            >
                                                0<p>Marked Spam</p>
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
                                                data-bs-target="#Forwards"
                                                type="button"
                                                role="tab"
                                                aria-controls="Forwards"
                                                aria-selected="false"
                                            >
                                                0<p>Forwards</p>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tab-content" id="myTabContent">
                                    <div
                                        class="tab-pane fade show active"
                                        id="Sent"
                                        role="tabpanel"
                                        aria-labelledby="home-tab"
                                    >
                                        1
                                    </div>
                                    <div
                                        class="tab-pane fade"
                                        id="Delivered"
                                        role="tabpanel"
                                        aria-labelledby="profile-tab"
                                    >
                                        2
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
                                                        <a href="#">
                                                            <img
                                                                src={
                                                                    IconFeature
                                                                }
                                                                className="img-fluid"
                                                                alt=""
                                                            />
                                                        </a>
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
                                                    <form
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
                                                            placeholder={t("Search")}
                                                            aria-label="Search"
                                                        />
                                                        <button
                                                            class="search-button-btn"
                                                            type="submit"
                                                        >
                                                            Search
                                                        </button>
                                                    </form>
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
                                                                    Contact
                                                                    Email
                                                                </th>
                                                                <th scope="col">
                                                                    Total Opens
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr className="table-success">
                                                                <td>
                                                                    ssafzal95@gmail.com
                                                                </td>
                                                                <td>7 Opens</td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    allawaljamal24242@gmail.com
                                                                </td>
                                                                <td>4 Opens</td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    asad.shahab26@gmail.com
                                                                </td>
                                                                <td>2 Opens</td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    moazzam.m.sial@gmail.com
                                                                </td>
                                                                <td>2 Opens</td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    ghana.ch@gmail.com
                                                                </td>
                                                                <td>2 Opens</td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    awaishussain626@gmail.com
                                                                </td>
                                                                <td>2 Opens</td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    Zohiaib.afzal.malik23@gmail.com
                                                                </td>
                                                                <td>2 Opens</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <div className="total-counts">
                                                        <div className="total-counts-content d-flex justify-content-between">
                                                            <div className="total-count-text">
                                                                <p>
                                                                    Toal Count
                                                                </p>
                                                            </div>
                                                            <div className="show-page">
                                                                Show 20{" "}
                                                                <img
                                                                    src={
                                                                        Path3972
                                                                    }
                                                                    alt=""
                                                                    className="img-fluid"
                                                                />{" "}
                                                                <span>
                                                                    per page
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div className="col-lg-6 recipient-activities-table-pagination">
                                                    <table className="table active-user-table-content">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">
                                                                    Opened by -
                                                                    ssafzal95@gmail.com
                                                                </th>
                                                                <th scope="col">
                                                                    <img
                                                                        src={
                                                                            Union5
                                                                        }
                                                                        alt=""
                                                                        className="img-fluid"
                                                                    />
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <p>
                                                                Sep 21, 2021
                                                                11:55 PM PKT
                                                            </p>
                                                            <p>
                                                                Sep 21, 2021
                                                                11:55 PM PKT
                                                            </p>
                                                            <p>
                                                                Sep 21, 2021
                                                                11:55 PM PKT
                                                            </p>
                                                            <p>
                                                                Sep 21, 2021
                                                                11:55 PM PKT
                                                            </p>
                                                            <p>
                                                                Sep 21, 2021
                                                                11:55 PM PKT
                                                            </p>
                                                            <p>
                                                                Sep 21, 2021
                                                                11:55 PM PKT
                                                            </p>
                                                            <p>
                                                                Sep 21, 2021
                                                                11:55 PM PKT
                                                            </p>
                                                            <p>
                                                                Sep 21, 2021
                                                                11:55 PM PKT
                                                            </p>
                                                            <p>
                                                                Sep 21, 2021
                                                                11:55 PM PKT
                                                            </p>
                                                        </tbody>
                                                    </table>
                                                    <div className="pagination-content">
                                                        <nav
                                                            className="pagination-navbar"
                                                            aria-label="Page navigation example"
                                                        >
                                                            <ul class="pagination">
                                                                <li class="page-item">
                                                                    <a
                                                                        class="page-link"
                                                                        href="#"
                                                                        aria-label="Previous"
                                                                    >
                                                                        <span aria-hidden="true">
                                                                            &laquo;
                                                                        </span>
                                                                    </a>
                                                                </li>
                                                                <li class="page-item">
                                                                    <a
                                                                        class="page-link"
                                                                        href="#"
                                                                    >
                                                                        1
                                                                    </a>
                                                                </li>
                                                                <li class="page-item to">
                                                                    to
                                                                </li>
                                                                <li class="page-item">
                                                                    <a
                                                                        class="page-link"
                                                                        href="#"
                                                                    >
                                                                        20
                                                                    </a>
                                                                </li>
                                                                <li class="page-item">
                                                                    <a
                                                                        class="page-link"
                                                                        href="#"
                                                                        aria-label="Next"
                                                                    >
                                                                        <span aria-hidden="true">
                                                                            &raquo;
                                                                        </span>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </nav>
                                                    </div>
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
                                        4
                                    </div>
                                    <div
                                        class="tab-pane fade"
                                        id="Clicked"
                                        role="tabpanel"
                                        aria-labelledby="contact-tab"
                                    >
                                        5
                                    </div>
                                    <div
                                        class="tab-pane fade"
                                        id="Replied"
                                        role="tabpanel"
                                        aria-labelledby="contact-tab"
                                    >
                                        <div className="icons-container">
                                            <div className="icons-content d-flex justify-content-between">
                                                <div className="icons-content-left d-flex">
                                                    <div className="IconFeature icon">
                                                        <a href="#">
                                                            <img
                                                                src={
                                                                    IconFeature
                                                                }
                                                                className="img-fluid"
                                                                alt=""
                                                            />
                                                        </a>
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
                                                    <form
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
                                                            placeholder={t("Search")}
                                                            aria-label="Search"
                                                        />
                                                        <button
                                                            class="search-button-btn"
                                                            type="submit"
                                                        >
                                                            Search
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="recipient-activities-table replied">
                                            <div className="row">
                                                <div className="col-lg-12 table-responsive">
                                                    <table className="em-table align-middle table">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">
                                                                    Contact
                                                                    Email
                                                                </th>
                                                                <th scope="col"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    shamsa.nadeem@gmail.com
                                                                </td>
                                                                <td>
                                                                    2 Replies
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    Annie.zain.javaid@gmail.com
                                                                </td>
                                                                <td>
                                                                    1 Replies
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    asfandyarathar87@gmail.com
                                                                </td>
                                                                <td>
                                                                    1 Replies
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    iamfatimabatool36@gmail.com
                                                                </td>
                                                                <td>
                                                                    1 Replies
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <div className="total-counts">
                                                        <div className="total-counts-content d-flex justify-content-between">
                                                            <div className="total-count-text">
                                                                <p>
                                                                    Toal Count
                                                                </p>
                                                            </div>
                                                            <div className="show-page">
                                                                Show 20{" "}
                                                                <img
                                                                    src={
                                                                        Path3972
                                                                    }
                                                                    alt=""
                                                                    className="img-fluid"
                                                                />{" "}
                                                                <span>
                                                                    per page
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div className="col-lg-6 recipient-activities-table-pagination">
                                                    <table className="table active-user-table-content">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">
                                                                    First Name
                                                                </th>
                                                                <th scope="col">
                                                                    Last Name
                                                                </th>
                                                                <th scope="col">
                                                                    <img
                                                                        src={
                                                                            Union5
                                                                        }
                                                                        alt=""
                                                                        className="img-fluid"
                                                                    />
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    Muhammad
                                                                    Ramin
                                                                </td>
                                                                <td>- -</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Halima</td>
                                                                <td>Zain</td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    Asfandyar
                                                                </td>
                                                                <td>Athar</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Fatima</td>
                                                                <td>Batool</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <div className="pagination-content">
                                                        <nav
                                                            className="pagination-navbar"
                                                            aria-label="Page navigation example"
                                                        >
                                                            <ul class="pagination">
                                                                <li class="page-item">
                                                                    <a
                                                                        class="page-link"
                                                                        href="#"
                                                                        aria-label="Previous"
                                                                    >
                                                                        <span aria-hidden="true">
                                                                            &laquo;
                                                                        </span>
                                                                    </a>
                                                                </li>
                                                                <li class="page-item">
                                                                    <a
                                                                        class="page-link"
                                                                        href="#"
                                                                    >
                                                                        1
                                                                    </a>
                                                                </li>
                                                                <li class="page-item to">
                                                                    to
                                                                </li>
                                                                <li class="page-item">
                                                                    <a
                                                                        class="page-link"
                                                                        href="#"
                                                                    >
                                                                        20
                                                                    </a>
                                                                </li>
                                                                <li class="page-item">
                                                                    <a
                                                                        class="page-link"
                                                                        href="#"
                                                                        aria-label="Next"
                                                                    >
                                                                        <span aria-hidden="true">
                                                                            &raquo;
                                                                        </span>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </nav>
                                                    </div>
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
                                        6
                                    </div>
                                    <div
                                        class="tab-pane fade"
                                        id="Marked"
                                        role="tabpanel"
                                        aria-labelledby="contact-tab"
                                    >
                                        7
                                    </div>
                                    <div
                                        class="tab-pane fade"
                                        id="Forwards"
                                        role="tabpanel"
                                        aria-labelledby="contact-tab"
                                    >
                                        8
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="tab-pane fade"
                            id="nav-contact"
                            role="tabpanel"
                            aria-labelledby="nav-contact-tab"
                        >
                            <div className="icons-container">
                                <div className="icons-content d-flex justify-content-between">
                                    <div className="icons-content-left d-flex">
                                        <div className="IconFeature icon">
                                            <a href="#">
                                                <img
                                                    src={IconFeature}
                                                    className="img-fluid"
                                                    alt=""
                                                />
                                            </a>
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
                                        <form
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
                                                placeholder={t("Search")}
                                                aria-label="Search"
                                            />
                                            <button
                                                class="search-button-btn"
                                                type="submit"
                                            >
                                                Search
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div className="recipient-activities-table">
                                <div className="row">
                                    <div className="col-lg-12">
                                        <table className="em-table align-middle table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">
                                                        Contact Email
                                                    </th>
                                                    <th scope="col">
                                                        Total Clicks
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr className="table-success">
                                                    <td>ssafzal95@gmail.com</td>
                                                    <td>7 Opens</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        allawaljamal24242@gmail.com
                                                    </td>
                                                    <td>4 Opens</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        asad.shahab26@gmail.com
                                                    </td>
                                                    <td>2 Opens</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        moazzam.m.sial@gmail.com
                                                    </td>
                                                    <td>2 Opens</td>
                                                </tr>
                                                <tr>
                                                    <td>ghana.ch@gmail.com</td>
                                                    <td>2 Opens</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        awaishussain626@gmail.com
                                                    </td>
                                                    <td>2 Opens</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Zohiaib.afzal.malik23@gmail.com
                                                    </td>
                                                    <td>2 Opens</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div className="total-counts">
                                            <div className="total-counts-content d-flex justify-content-between">
                                                <div className="total-count-text">
                                                    <p>Toal Count</p>
                                                </div>
                                                <div className="show-page">
                                                    Show 20{" "}
                                                    <img
                                                        src={Path3972}
                                                        alt=""
                                                        className="img-fluid"
                                                    />{" "}
                                                    <span>per page</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="col-lg-6 recipient-activities-table-pagination">
                                        <table className="table active-user-table-content">
                                            <thead>
                                                <tr>
                                                    <th scope="col">
                                                        Clicked by -
                                                        ssafzal95@gmail.com
                                                    </th>
                                                    <th scope="col">
                                                        <img
                                                            src={Union5}
                                                            alt=""
                                                            className="img-fluid"
                                                        />
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <p>Sep 21, 2021 11:55 PM PKT</p>
                                                <p>Sep 21, 2021 11:55 PM PKT</p>
                                                <p>Sep 21, 2021 11:55 PM PKT</p>
                                                <p>Sep 21, 2021 11:55 PM PKT</p>
                                                <p>Sep 21, 2021 11:55 PM PKT</p>
                                                <p>Sep 21, 2021 11:55 PM PKT</p>
                                                <p>Sep 21, 2021 11:55 PM PKT</p>
                                                <p>Sep 21, 2021 11:55 PM PKT</p>
                                                <p>Sep 21, 2021 11:55 PM PKT</p>
                                            </tbody>
                                        </table>
                                        <div className="pagination-content">
                                            <nav
                                                className="pagination-navbar"
                                                aria-label="Page navigation example"
                                            >
                                                <ul class="pagination">
                                                    <li class="page-item">
                                                        <a
                                                            class="page-link"
                                                            href="#"
                                                            aria-label="Previous"
                                                        >
                                                            <span aria-hidden="true">
                                                                &laquo;
                                                            </span>
                                                        </a>
                                                    </li>
                                                    <li class="page-item">
                                                        <a
                                                            class="page-link"
                                                            href="#"
                                                        >
                                                            1
                                                        </a>
                                                    </li>
                                                    <li class="page-item to">
                                                        to
                                                    </li>
                                                    <li class="page-item">
                                                        <a
                                                            class="page-link"
                                                            href="#"
                                                        >
                                                            20
                                                        </a>
                                                    </li>
                                                    <li class="page-item">
                                                        <a
                                                            class="page-link"
                                                            href="#"
                                                            aria-label="Next"
                                                        >
                                                            <span aria-hidden="true">
                                                                &raquo;
                                                            </span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </nav>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="tab-pane fade"
                            id="nav-bounce"
                            role="tabpanel"
                            aria-labelledby="nav-bounce-tab"
                        >
                            <div className="icons-container">
                                <div className="icons-content d-flex justify-content-between">
                                    <div className="icons-content-left d-flex">
                                        <div className="IconFeature icon">
                                            <a href="#">
                                                <img
                                                    src={IconFeature}
                                                    className="img-fluid"
                                                    alt=""
                                                />
                                            </a>
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
                                        <form
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
                                                placeholder={t("Search")}
                                                aria-label="Search"
                                            />
                                            <button
                                                class="search-button-btn"
                                                type="submit"
                                            >
                                                Search
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div className="recipient-activities-table nav-bounce">
                                <div className="row">
                                    <div className="col-lg-6">
                                        <table className="table bounces-and-autoreply-table-content">
                                            <thead>
                                                <tr>
                                                    <th scope="col">
                                                        Contact Email
                                                    </th>
                                                    <th scope="col">
                                                        Bounce Reason
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        ruhaanruhaan24@gmail.com
                                                    </td>
                                                    <td>
                                                        Permanent failure
                                                        :bad-mailbox
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        rizwan@buraaqautos.com
                                                    </td>
                                                    <td>
                                                        Permanent failure
                                                        :bad-mailbox
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div className="total-counts">
                                            <div className="total-counts-content d-flex justify-content-between">
                                                <div className="total-count-text">
                                                    <p>Toal Count</p>
                                                </div>
                                                <div className="show-page">
                                                    Show 20{" "}
                                                    <img
                                                        src={Path3972}
                                                        alt=""
                                                        className="img-fluid"
                                                    />{" "}
                                                    <span>per page</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="col-lg-6 bounces-and-autoreply-table-pagination">
                                        <table className="table active-user-table-content">
                                            <thead>
                                                <tr>
                                                    <th scope="col">
                                                        First Name
                                                    </th>
                                                    <th scope="col">
                                                        Last Name
                                                    </th>
                                                    <th scope="col">
                                                        <img
                                                            src={Union5}
                                                            alt=""
                                                            className="img-fluid"
                                                        />
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Ruhaan</td>
                                                    <td>Ali</td>
                                                </tr>
                                                <tr>
                                                    <td>Muhammad Aban</td>
                                                    <td>- -</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div className="pagination-content">
                                            <nav
                                                className="pagination-navbar"
                                                aria-label="Page navigation example"
                                            >
                                                <ul class="pagination">
                                                    <li class="page-item">
                                                        <a
                                                            class="page-link"
                                                            href="#"
                                                            aria-label="Previous"
                                                        >
                                                            <span aria-hidden="true">
                                                                &laquo;
                                                            </span>
                                                        </a>
                                                    </li>
                                                    <li class="page-item">
                                                        <a
                                                            class="page-link"
                                                            href="#"
                                                        >
                                                            1
                                                        </a>
                                                    </li>
                                                    <li class="page-item to">
                                                        to
                                                    </li>
                                                    <li class="page-item">
                                                        <a
                                                            class="page-link"
                                                            href="#"
                                                        >
                                                            20
                                                        </a>
                                                    </li>
                                                    <li class="page-item">
                                                        <a
                                                            class="page-link"
                                                            href="#"
                                                            aria-label="Next"
                                                        >
                                                            <span aria-hidden="true">
                                                                &raquo;
                                                            </span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </nav>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="tab-pane fade"
                            id="nav-stat"
                            role="tabpanel"
                            aria-labelledby="nav-stat-tab"
                        >
                            ...
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default withTranslation()(Newreport);
