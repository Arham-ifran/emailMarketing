import React, { useEffect, useState, forwardRef } from 'react';

import { withTranslation } from 'react-i18next';
import "./AddNumberStyles.css";

const AddNumbersInput = forwardRef((props, ref) => {
  const { t } = props;

  const [items, setItems] = useState([]);
  const [value, setValue] = useState('');
  const [error, setError] = useState();

  const handleKeyDown = evt => {
    if (["Enter", "Tab", ","].includes(evt.key)) {
      evt.preventDefault();

      // console.log(document.getElementById('add_numbers').value);
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
    var numbers = paste.match(/(\+)([1-9]{2})(\d{10})/g);

    if (numbers) {
      var toBeAdded = numbers.filter(number => !isInList(number));

      setItems([...items, ...toBeAdded]);
      props.changeList([...items, ...toBeAdded])
    }
  };

  const getItems = () => {
    return items;
  }

  const isValid = (number) => {
    let error = null;

    if (isInList(number)) {
      error = number + " " + t('has already been added.');
    }

    if (!isnumber(number)) {
      error = number + " " + t('is not a valid number.');
    }

    if (error) {
      setError(error);
      return false;
    }

    return true;
  }

  const isInList = (number) => {
    return items.includes(number);
  }

  const isnumber = (number) => {
    return /(\+)([1-9]{2})(\d{10})/.test(number);
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
          id="add_numbers"
          className={"form-control " + (error && " has-error")}
          value={value}
          placeholder={t("type_or_paste_number")}
          onKeyDown={handleKeyDown}
          onChange={handleChange}
          onPaste={handlePaste}
        />
      </div>

      {error && <p className="error error-space"><span className='error-space'>{error}</span> </p>}
    </>
  );

})

export default withTranslation()(AddNumbersInput);