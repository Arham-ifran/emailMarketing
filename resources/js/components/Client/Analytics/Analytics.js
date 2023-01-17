import React, { useEffect, useState } from "react";
import rocket from "../../../assets/images/rocket.svg";
import { Container, Row, Col } from "react-bootstrap";
import map from "../../../assets/images/map.png";
import { withTranslation } from 'react-i18next';
function Analytics(props) {

  const [sectionText, setSectionText] = useState();
  const [contents, setContents] = useState([]);
  const { t } = props;
  useEffect(() => {
    if(props.contents.length){
      setContents(props.contents);
      var allcontents = props.contents;
      setSectionText(allcontents.find(content => content.id == 8));
    }
  }, [props]);

  return (
    <>
      <section className="analytics">
        <div className="container-width container-width-padding">
          <div className="analytics-header text-center">
            <h2 className="all-h2">{t('Analytics Of Your Campaign')}</h2>
            <div className="analytics-header-image">
              <img className="img-fluid" src={map} alt=" Site Logo" />
            </div>
          </div>
          
            { sectionText ? 
              <div className="mb-lg-5 row" dangerouslySetInnerHTML={{__html: sectionText.description}} />
            : ""}
            
        </div>
      </section>
    </>
  );
}

export default withTranslation()(Analytics);
