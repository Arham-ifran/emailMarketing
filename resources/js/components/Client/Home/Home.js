import React, { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import { Form, Button } from "react-bootstrap";
import Banner from "../Banner/Banner";
import Video from "../Video/Video";
import Growing from "../Growing/Growing";
import Works from "../Works/Works";
import Services from "../Services/Services";
import Analytics from "../Analytics/Analytics";
import Plan from "../Plan/Plan";
import Questions from "../Questions/Questions";
import Footer from "../Footer/Footer";
import ContactUs from "../ContactUs/ContactUs";
import Spinner from "../../includes/spinner/Spinner";
import { withTranslation } from 'react-i18next';
// import SiteLogo from '../../assets/images/logo.svg';

function Home(props) {
  const { t } = props;
  const [loading, setLoading] = useState(false);
  const [contents, setContents] = useState([]);

  useEffect(() => {
    const getSections = () => {
      setLoading(true);
      axios
        .get("/api/get-home-sections?lang=" + localStorage.lang)
        .then((response) => {
          setLoading(false);
          // console.log(response.data);
          setContents(response.data.data);
        })
        .catch((error) => {
          setLoading(false);
        });
    };
    getSections();
  }, []);

  return (
    <React.Fragment>
      {/* {contents.length?
      contents.map(content => (
        <div className="" dangerouslySetInnerHTML={{__html: content.description}}></div>
      ))
      :""} */}
      <Banner contents={contents} />
      <Video contents={contents} />
      <Growing contents={contents} />
      <Works contents={contents} />
      <Services contents={contents} />
      <Analytics contents={contents} />
      <Plan contents={contents} />
      <Questions contents={contents} />
    </React.Fragment>
  );
}

export default withTranslation()(Home);
