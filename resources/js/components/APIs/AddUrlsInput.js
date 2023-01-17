import React, { useEffect, useState, forwardRef, useImperativeHandle } from 'react';

import { withTranslation } from 'react-i18next';
import "./AddUrlsStyles.css";

const AddUrlsInput = forwardRef((props, ref) => {
  const { t } = props;

  const [value, setValue] = useState('');
  const [error, setError] = useState();

  const handleKeyDown = evt => {
    if (["Enter", "Tab", ","].includes(evt.key)) {
      evt.preventDefault();

      // console.log(document.getElementById('add_urls').value);
      var val = value.trim();

      if (val && isValid(val)) {
        setValue("")
        // setItems();
        props.changeEndpointUrls([...props.parentUrls, val]);
      }
    }
  };

  const handleChange = evt => {
    props.clearErrors();
    setValue(evt.target.value)
    setError(null);
  };

  const handleDelete = item => {
    // setItems( items.filter(i => i !== item) );
    props.changeEndpointUrls(props.parentUrls.filter(i => i !== item));
  };

  const handlePaste = evt => {
    evt.preventDefault();

    var paste = evt.clipboardData.getData("text");
    var urls = paste.match(/^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:[/?#]\S*)?$/i);

    if (urls) {
      var toBeAdded = urls.filter(url => !isInList(url));

      // setItems( [...items, ...toBeAdded] );
      props.changeEndpointUrls([...props.parentUrls, ...toBeAdded]);
    }
  };

  const getItems = () => {
    return props.parentUrls;
  }

  const isValid = (url) => {
    let error = null;

    if (isInList(url)) {
      error = `${url} has already been added.`;
    }

    if (!isUrl(url)) {
      error = `${url} is not a valid url.`;
    }

    if (error) {
      setError(error);
      return false;
    }

    return true;
  }

  const isInList = (url) => {
    return props.parentUrls.includes(url);
  }

  const isUrl = (url) => {
    return /^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:[/?#]\S*)?$/i.test(url);
  }

  return (
    <>
      {props.parentUrls.map(item => (
        <div className="tag-item" key={item}>
          {item}
          <button
            type="button"
            className="button"
            onClick={() => handleDelete(item)}
          >
            &times;
          </button>
        </div>
      ))}

      <div className="">
        <input
          id="add_urls"
          className={"form-control " + (error && " has-error")}
          value={value}
          placeholder={t('type_or_paste_url')}
          onKeyDown={handleKeyDown}
          onChange={handleChange}
          onPaste={handlePaste}
        />
      </div>

      {error && <p className="error" id="there_is_error">{error}</p>}
    </>
  );

})

export default withTranslation()(AddUrlsInput);