import jQuery from 'jquery'
window.$ = jQuery;

import './bootstrap';

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

import './common';

import InputEditable from "./InputEditable";
new InputEditable({});
