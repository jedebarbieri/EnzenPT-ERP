import jQuery from 'jquery'
window.$ = jQuery;

import './bootstrap';

$.ajaxSetup({
    beforeSend: function(xhr) {
        xhr.setRequestHeader('Authorization', 'Bearer ' + window.Laravel.apiToken);
    }
});

window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + window.Laravel.apiToken;

// Validation Library
import 'jquery-validation';

// DataTable Library
import 'datatables.net'
import 'datatables.net-responsive'
import 'datatables.net-buttons'
import 'datatables.net-bs4'
import 'datatables.net-responsive-bs4'
import 'datatables.net-buttons-bs4'

// Select2 Library
import select2 from 'select2'; select2();

import Inputmask from "inputmask";
Inputmask();

import 'admin-lte';

import MaskedInput from "./MaskedInput";
window.MaskedInput = MaskedInput;
new MaskedInput({});

import './common';