import React from 'react';
import campaignPreviewImg from '../../assets/images/img-01.jpg';
import './CampaignPreview.css';
function CampaignPreview(props) {
	return (
		<React.Fragment>
			<div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3">
				<div className="page-title">
					<h2>Campaign Preview</h2>
				</div>
			</div>
			<div className="campaign-preview-widget p-3 bg-white rounded-box-shadow">
				<strong className="widget-heading d-block mb-3">Welcome To Email Marketing Campaign</strong>
				<div className="d-flex flex-column flex-sm-row">
					<div className="image-holder">
						<img className="img-fluid" src={campaignPreviewImg} alt="" />
					</div>
					<div className="detail-holder flex-fill">
						<small className="template-name text-uppercase">Presentation Template</small>
						<h3 className="mb-3">Marketing Campaign Plan</h3>
						<p>Go-to-market strategy and deliverable plan.</p>
						<time className="campaign-date d-block"datetime="2021-08-07">07-08-2021</time>
					</div>
				</div>
			</div>
		</React.Fragment>
	);
}

export default CampaignPreview;