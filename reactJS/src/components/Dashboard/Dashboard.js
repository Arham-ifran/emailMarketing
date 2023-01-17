import React from 'react';
import { Container, Row, Col } from 'react-bootstrap';
import Graph01 from '../../assets/images/graph-01.jpg';
import Graph02 from '../../assets/images/graph-02.jpg';
import Graph03 from '../../assets/images/graph-03.jpg';

import './Dashboard.css';
function Dashboard(props) {
	return (
		<React.Fragment>
			<Container fluid>
				<Row>
					<Col xl="4" xs="12">
						<div className="graph-box bg-white rounded-box-shadow">
							<h3 className="mb-3">Campaign Reach</h3>
							<div className="campaign-graph-holder">
								<img className="img-fluid" src={Graph01} alt="graph" />
							</div>
						</div>
					</Col>
					<Col xl="8" xs="12">
						<div className="graph-box bg-white rounded-box-shadow">
							<h3 className="mb-3">Clicks &amp; Open Rate</h3>
							<div className="campaign-graph-holder">
								<img className="img-fluid" src={Graph02} alt="graph" />
							</div>
						</div>
					</Col>
				</Row>
				<Row>
					<Col xxl="8" xl="6" xs="12">
						<div className="graph-box bg-white rounded-box-shadow">
							<h3 className="mb-3">Clicks &amp; Open Rate</h3>
							<div className="campaign-graph-holder">
								<img className="img-fluid" src={Graph03} alt="graph" />
							</div>
						</div>
					</Col>
					<Col xxl="4" xl="6" xs="12">
						<div className="graph-box bg-white rounded-box-shadow">
							<div className="stats-blocks d-flex">
								<div className="stat-block d-flex flex-column align-items-center text-center">
									<strong className="stat-value mb-2">2340</strong>
									<span className="stat-name text-uppercase">Total Contacts</span>
								</div>
								<div className="stat-block d-flex flex-column align-items-center text-center">
									<strong className="stat-value mb-2">230</strong>
									<span className="stat-name text-uppercase">Draft Contacts</span>
								</div>
							</div>
							<div className="stats-blocks d-flex">
								<div className="stat-block d-flex flex-column align-items-center text-center">
									<strong className="stat-value mb-2">143</strong>
									<span className="stat-name text-uppercase">Total Subscribers</span>
								</div>
								<div className="stat-block d-flex flex-column align-items-center text-center">
									<strong className="stat-value mb-2">3125</strong>
									<span className="stat-name text-uppercase">Total Unsubscribers</span>
								</div>
							</div>
						</div>
					</Col>
					
				</Row>
			</Container>
		</React.Fragment>
	);
}

export default Dashboard;