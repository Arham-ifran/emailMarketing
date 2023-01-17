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
      error = email + " " + t('is not a valid email address.');
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
          placeholder={t('type_or_paste_email')}
          onKeyDown={handleKeyDown}
          onChange={handleChange}
          onPaste={handlePaste}
        />
      </div>

      {error && <p className="error error-space"><span className='error-space'>{error}</span> </p>}
    </>
  );

})

// class addEmailsInput extends React.Component {
//   state = {
//     items: [],
//     value: "",
//     error: null
//   };

//   handleKeyDown = evt => {
//     if (["Enter", "Tab", ","].includes(evt.key)) {
//       evt.preventDefault();

//       var value = this.state.value.trim();

//       if (value && this.isValid(value)) {
//         this.setState({
//           items: [...this.state.items, this.state.value],
//           value: ""
//         });
//       }
//     }
//   };

//   handleChange = evt => {
//     this.setState({
//       value: evt.target.value,
//       error: null
//     });
//   };

//   handleDelete = item => {
//     this.setState({
//       items: this.state.items.filter(i => i !== item)
//     });
//   };

//   handlePaste = evt => {
//     evt.preventDefault();

//     var paste = evt.clipboardData.getData("text");
//     var emails = paste.match(/[\w\d\.-]+@[\w\d\.-]+\.[\w\d\.-]+/g);

//     if (emails) {
//       var toBeAdded = emails.filter(email => !this.isInList(email));

//       this.setState({
//         items: [...this.state.items, ...toBeAdded]
//       });
//     }
//   };

//   isValid(email) {
//     let error = null;

//     if (this.isInList(email)) {
//       error = `${email} has already been added.`;
//     }

//     if (!this.isEmail(email)) {
//       error = `${email} is not a valid email address.`;
//     }

//     if (error) {
//       this.setState({ error });

//       return false;
//     }

//     return true;
//   }

//   isInList(email) {
//     return this.state.items.includes(email);
//   }

//   isEmail(email) {
//     return /[\w\d\.-]+@[\w\d\.-]+\.[\w\d\.-]+/.test(email);
//   }

//   render() {
//     return (
//       <>
//         {this.state.items.map(item => (
//           <div className="tag-item" key={item}>
//             {item}
//             <button
//               type="button"
//               className="button"
//               onClick={() => this.handleDelete(item)}
//             >
//               &times;
//             </button>
//           </div>
//         ))}

//         <input
//           className={"input " + (this.state.error && " has-error")}
//           value={this.state.value}
//           placeholder="type_or_paste_email"
//           onKeyDown={this.handleKeyDown}
//           onChange={this.handleChange}
//           onPaste={this.handlePaste}
//         />

//         {this.state.error && <p className="error">{this.state.error}</p>}
//       </>
//     );
//   }
// }

export default withTranslation()(AddEmailsInput);