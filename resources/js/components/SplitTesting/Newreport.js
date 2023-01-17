import React, { useEffect, useState, useRef } from 'react';
import { Container, Form, Row, Col, Button, Modal, Table } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faListAlt } from '@fortawesome/free-regular-svg-icons'
import Spinner from "../includes/spinner/Spinner";
import Moment from "react-moment";
import moment from 'moment-timezone';
import Pagination from "react-js-pagination";
import ReportHistory from './ReportHistory';
import { withTranslation } from 'react-i18next';
const Newreport = (props) => {
    const { t } = props;
    const [pageLoading, setPageLoading] = useState(false);
    const [errors, setErrors] = useState([]);
    const [refresh, setRefresh] = useState(0)
    const [pageNumber, setPageNumber] = useState(1);
    const [perPage, setperPage] = useState(10);
    const [totalItems, setTotalItems] = useState(0);
    const [pageRange, setPageRange] = useState(10);

    const [showHistory, setShowHistory] = useState(0);
    const [historyID, setHistoryID] = useState('');

    const [campaign, setCampaign] = useState([]);
    const [reports, setReports] = useState([]);
    const [campaignInitiated, setCampaignInitiated] = useState("");
    const [campaignProcessed, setCampaignProcessed] = useState("");
    const [campaignStopped, setCampaignStopped] = useState("");

    const getReport = (campId) => {
        setPageLoading(true);
        axios
            .get("/api/get-email-campaign-report-histories/" + campId + "?page=" + pageNumber + "&lang=" + localStorage.lang)
            .then((response) => {
                setPageLoading(false);
                const received_data = response.data;
                setCampaign(received_data.campaign);
                setReports(received_data.data);
                setCampaignInitiated(received_data.campaign.initiated_at);
                setCampaignProcessed(received_data.campaign.processed_at);
                setCampaignStopped(received_data.campaign.stopped_at);
                setTotalItems(received_data.meta.total);
            })
            .catch((error) => {
                if (error.response.data.errors) {
                    setErrors(error.response.data.errors);
                }
                setPageLoading(false);
            });
    };
    useEffect(() => {
        let parseUriSegment = window.location.pathname.split("/");
        if (
            parseUriSegment.indexOf("split-testing") &&
            parseUriSegment.indexOf("report") != -1
        ) {
            getReport(parseUriSegment[2]);
        }
    }, [refresh]);

    return (
        <>
            {pageLoading ? <Spinner /> : null}
            {!showHistory ?
                <section className="reports">
                    <div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between ml-3">
                        <div className="page-title">
                            <h2 className="mb-3">{t('Campaign Name')}: <span className="muted"> {campaign.name} </span> </h2>
                        </div>
                    </div>
                    <Container fluid>
                        <Row>
                            <Col xl="4" lg="6" md="4" sm="6" xs="12">
                                <div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
                                    <span className="title">{t('Times To Send')}</span>
                                    <span className="value">{campaign.no_of_time + reports.length}</span>
                                </div>
                            </Col>
                            <Col xl="4" lg="6" md="4" sm="6" xs="12">
                                <div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
                                    <span className="title">Times Sent</span>
                                    <span className="value">{reports.length}</span>
                                </div>
                            </Col>
                            <Col xl="4" lg="6" md="4" sm="6" xs="12">
                                <div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
                                    <span className="title">{t('Sends remaining')}</span>
                                    <span className="value">{campaign.no_of_time ? campaign.no_of_time : 0}</span>
                                </div>
                            </Col>
                            {campaignStopped ?
                                <Col xl="4" lg="6" md="4" sm="6" xs="12">
                                    <div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
                                        <span className="title">{t('Stopped at')}</span>
                                        <span className="value"><Moment format="DD MMMM YYYY hh:mm:ss">
                                            {moment.tz(moment(campaignStopped).utc(), localStorage.timezone)}
                                        </Moment></span>
                                    </div>
                                </Col>
                                :
                                <React.Fragment>
                                    <Col xl="4" lg="6" md="4" sm="6" xs="12">
                                        <div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
                                            <span className="title">{t('Initiated at')}</span>
                                            <span className="value"><Moment format="DD MMMM YYYY hh:mm:ss">
                                                {moment.tz(moment(campaignInitiated).utc(), localStorage.timezone)}
                                            </Moment></span>
                                        </div>
                                    </Col>
                                    <Col xl="4" lg="6" md="4" sm="6" xs="12">
                                        <div className="stat-box bg-white rounded-box-shadow d-flex flex-column align-items-center">
                                            <span className="title">{t('Processed at')}</span>
                                            <span className="value"><Moment format="DD MMMM YYYY hh:mm:ss">
                                                {moment.tz(moment(campaignProcessed).utc(), localStorage.timezone)}
                                            </Moment></span>
                                        </div>
                                    </Col>
                                </React.Fragment>}
                        </Row>
                        <div className="p-sm-5 p-2 bg-white rounded-box-shadow" >
                            <div className="">
                                <h5>{t('History')}:</h5>
                                <div className="table-responsive">
                                    <Table className="table em-table align-middle">
                                        <thead>
                                            <tr>
                                                <th>{t('Sr.')}</th>
                                                <th>{t('Contacts selected')}</th>
                                                <th>{t('Successfully Sent')}</th>
                                                <th>{t('Sending Fails')}</th>
                                                <th>{t('Date Sent')}</th>
                                                <th>{t('Actions')}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {reports.length ?
                                                reports.map((report, index) => (
                                                    <tr key={index}>
                                                        <td>{(pageNumber - 1) * perPage + index + 1}</td>
                                                        <td>{report.success + report.fail}</td>
                                                        <td>{report.success}</td>
                                                        <td>{report.fail}</td>
                                                        <td><Moment format="DD MMMM YYYY">
                                                            {moment.tz(moment(report.created_at).utc(), localStorage.timezone)}
                                                        </Moment></td>
                                                        <td>
                                                            <ul className="action-icons list-unstyled">
                                                                <li><button onClick={() => { setShowHistory(1); setHistoryID(report.hash_id) }} className="view-icon" title={t("View")}><FontAwesomeIcon icon={faListAlt} /></button></li>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                ))
                                                :
                                                <tr>
                                                    <td className="text-center" colSpan="6">
                                                        {t('No Campaigns Found')}
                                                    </td>
                                                </tr>
                                            }
                                        </tbody>
                                    </Table>
                                </div>
                                {/* pagination starts here */}
                                <div className="mt-2">
                                    <Pagination
                                        activePage={pageNumber}
                                        itemsCountPerPage={perPage}
                                        totalItemsCount={totalItems}
                                        pageRangeDisplayed={pageRange}
                                        onChange={(e) => setPageNumber(e)}
                                    />
                                </div>
                                {/* pagination ends here */}
                            </div>
                        </div>
                    </Container>
                </section>
                :
                <section>
                    <ReportHistory history={historyID} back={() => setShowHistory(0)}></ReportHistory>
                </section>
            }
        </>
    );
};

export default withTranslation()(Newreport);
