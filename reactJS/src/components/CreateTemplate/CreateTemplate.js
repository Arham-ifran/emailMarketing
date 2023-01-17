import React from 'react';
import { Link } from 'react-router-dom';
import { Container } from 'react-bootstrap';
import './CreateTemplate.css';
function CreateTemplate(props) {
	return (
		<React.Fragment>
			<Container fluid>
				<div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
					<div className="page-title">
						<h1>Create Template</h1>
					</div>
				</div>
				<div className="rounded-box-shadow bg-white">
					<div id="editor"></div>
					<div className="btns-holder right-btns d-flex flex-row-reverse pt-5 flex-sm-row flex-column ">
						<Link to="/my-mailing-list" className="btn btn-primary ms-3 mb-3"><span>Next</span></Link>
						<Link to="/split-testing" className="btn btn-secondary ms-3 mb-3"><span>Back</span></Link>
					</div>
				</div>
			</Container>
		</React.Fragment>
	);
}

export default CreateTemplate;