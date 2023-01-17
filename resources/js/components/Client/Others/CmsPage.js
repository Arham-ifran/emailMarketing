import React, { useEffect, useState } from "react";
import { Container, Row, Col } from "react-bootstrap";
import Spinner from '../../includes/spinner/Spinner';
import { withTranslation } from 'react-i18next';

function CmsPages(props) {
    const { t } = props;
    const [cms_page, setCmsPage] = useState({});
    const [loading, setLoading] = useState(false);
    const [page_slug, setPageSlug] = useState('');

    useEffect(() => {
        let parseUriSegment = window.location.pathname.split("/");
        if (parseUriSegment.indexOf('pages')) {
            console.log(parseUriSegment[2]);
            getCmsPage(parseUriSegment[2]);
        }
    }, [props]);

    const getCmsPage = (slug) => {
        setLoading(true);
        setPageSlug(slug);
        axios.get('/api/cms-pages/detail?slug=' + slug + '&lang=' + localStorage.lang)
            .then(response => {
                if (response.data) {
                    setCmsPage(response.data.cmsPage)
                    setLoading(false);
                }
                else {
                    window.location.replace("/");
                }
                $('html, body').animate({ scrollTop: 0 }, 0);
            })
            .catch(error => {
                setLoading(false);
            })
    }

    return (
        <>
            {loading ? <Spinner /> : null}
            <div className="main-content cms-page-info">
                <div className="container p-md-5 p-3">
                    <div className="cms-detail" dangerouslySetInnerHTML={{ __html: cms_page.content }} />
                </div>
                {/* {
                !localStorage.jwt_token ? (
                    <section className="get-started">
                        <div className="container">
                            <div className="get-content">
                                <h2>{t('cms_page.related_links_title')}</h2>
                                <p>{t('cms_page.related_links_description_1')}<br />
                                    {t('cms_page.related_links_description_2')}</p>
                                <Link to="/signup" className="btn btn-theme-blue">{t('get_started')}</Link>
                            </div>
                        </div>
                    </section>
                ) : null
            } */}
            </div>
        </>
    );
}

export default withTranslation()(CmsPages);
