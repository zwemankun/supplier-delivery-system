import { startStimulusApp } from '@symfony/stimulus-bridge';
import './bootstrap';
import './styles/app.css';

// Import Bootstrap and jQuery
import 'bootstrap';
import $ from 'jquery';

global.$ = global.jQuery = $;