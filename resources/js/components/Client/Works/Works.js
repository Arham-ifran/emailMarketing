import React, { useEffect, useState } from "react";
import rocket from "../../../assets/images/rocket.svg";
import megaphone from "../../../assets/images/megaphone.svg";
import mind from "../../../assets/images/mind.svg";
import clock from "../../../assets/images/clock.svg";
import { Container, Row, Col } from "react-bootstrap";

function Works(props) {

  const [sectionText, setSectionText] = useState();
  const [sectionExplaination, setSectionExplaination] = useState();
  const [contents, setContents] = useState([]);

  useEffect(() => {
    if(props.contents.length){
      setContents(props.contents);
      var allcontents = props.contents;
      setSectionText(allcontents.find(content => content.id == 5));
      setSectionExplaination(allcontents.find(content => content.id == 6));
    }
  }, [props]);

  return (
    <>
      <section className="works">
        <div className="container-width container-width-padding">
          <div className="works-heading text-center d-flex flex-column align-items-center">
            { sectionText ? 
              <div dangerouslySetInnerHTML={{__html: sectionText.description}} />
            : ""}
          </div>
            { sectionExplaination ? 
              <div className="row" dangerouslySetInnerHTML={{__html: sectionExplaination.description}} />
            : ""}
        </div>
      </section>
    </>
  );
}

export default Works;
