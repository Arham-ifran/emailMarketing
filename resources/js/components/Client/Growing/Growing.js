import React, { useEffect, useState } from "react";
import { Container, Row, Col } from "react-bootstrap";
import growing from "../../../assets/images/client/growing-img.png";
import { withTranslation } from 'react-i18next';

function Growing(props) {
  const { t } = props;
  const [sectionText, setSectionText] = useState();
  const [sectionData, setSectionData] = useState();
  const [contents, setContents] = useState([]);

  useEffect(() => {
    if(props.contents.length){
      setContents(props.contents);
      var allcontents = props.contents;
      setSectionText(allcontents.find(content => content.id == 3));
      setSectionData(allcontents.find(content => content.id == 4));
    }
  }, [props]);

  return (
    <>
      <section className="growing">
        <div className="container-width">
          <Row className="align-items-center">
            <Col md="4" xs="12">
              <div className="growing-content">
                { sectionText ? 
                  <div dangerouslySetInnerHTML={{__html: sectionText.description}} />
                : ""}
              </div>
            </Col>
            <Col md="4" xs="12">
              <div className="growing-image">
                <img className="img-fluid" src={growing} alt={t('Site image')} />
              </div>
            </Col>
            <Col md="4" xs="12">
              { sectionData ? 
                <div dangerouslySetInnerHTML={{__html: sectionData.description}} />
              : ""}
            </Col>
          </Row>
        </div>
      </section>
    </>
  );
}

export default withTranslation()(Growing);
