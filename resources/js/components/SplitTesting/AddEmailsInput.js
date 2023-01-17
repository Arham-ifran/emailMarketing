import React, { useEffect, useState, forwardRef } from 'react';

import { withTranslation } from 'react-i18next';
import "./AddEmailStyles.css";

const AddEmailsInput = forwardRef((props, ref) => {
  const { t } = props;

  const [items, setItems] = useState([]);
  const [value, setValue] = useState('');
  const [error, setError] = useState();

  const handleKeyDown = evt => {
    if (["Enter", "Tab", ","].includes(evt.key)) {
      evt.preventDefault();

      // console.log(document.getElementById('add_emails').value);
      var val = value.trim();

      if (val && isValid(val)) {
        setValue("")
        setItems([...items, val]);
        props.changeList([...items, val]);
      }
    }
  };

  const handleChange = evt => {
    setValue(evt.target.value)
    setError(null);
  };

  const handleDelete = item => {
    setItems(items.filter(i => i !== item));
    props.changeList(items.filter(i => i !== item));
  };

  const handlePaste = evt => {
    evt.preventDefault();

    var paste = evt.clipboardData.getData("text");
    var emails = paste.match(/[\w\d\.-]+@[\w\d\.-]+\.[\w\d\.-]+/g);

    if (emails) {
      var toBeAdded = emails.filter(email => !isInList(email));

      setItems([...items, ...toBeAdded]);
      props.changeList([...items, ...toBeAdded]);
    }
  };

  const getItems = () => {
    return items;
  }

  const isValid = (email) => {
    let error = null;

    if (isInList(email)) {
      error = email + " " + t('has already been added.');
    }

    if (!isEmail(email)) {
      error = email + " " +  t('is not a valid email address.');
    }

    if (error) {
      setError(error);
      return false;
    }

    return true;
  }

  const isInList = (email) => {
    return items.includes(email);
  }

  const isEmail = (email) => {
    return /[\w\d\.-]+@[\w\d\.-]+\.[\w\d\.-]+/.test(email);
  }

  return (
    <>
      {items.map(item => (
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
          id="add_emails"
          className={"form-control " + (error && " has-error")}
          value={value}
          placeholder={t("type_or_paste_email")}
          onKeyDown={handleKeyDown}
          onChange={handleChange}
          onPaste={handlePaste}
        />
      </div>

      {error && <p className="error"><span className='error-space'>{error}</span> </p>}
    </>
  );

})

export default withTranslation()(AddEmailsInput);