import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import youtube from "../../../assets/images/youtube.svg";
function Video(props) {

  const [sectionText, setSectionText] = useState();
  const [contents, setContents] = useState([]);

  useEffect(() => {
    if (props.contents.length) {
      setContents(props.contents);
      var allcontents = props.contents;
      setSectionText(allcontents.find(content => content.id == 2));
    }
  }, [props]);

  return (
    <>
      {sectionText ?
        <section className="video-section">
          <div className="container-width">
            <div className="video-section-content d-flex align-items-center justify-content-center">
              <div className="social-icons-wrap">
                <ul className="list-inline d-flex align-items-center">
                  <li>
                    <Link to="/" className="social-links">
                      <img src={youtube} alt=" icon" />
                    </Link>
                  </li>
                </ul>
              </div>
              <div dangerouslySetInnerHTML={{ __html: sectionText.description }} />
            </div>
          </div>
        </section>
        : ""}
    </>
  );
}

export default Video;
