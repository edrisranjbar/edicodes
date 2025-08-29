import './assets/css/fonts.css'
import './assets/css/tailwind.css'
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import Particles from 'vue3-particles'

/* Import FontAwesome core */
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

/* Import specific icons */
import { 
  faEnvelope, faHome, faHeart, faBook, faServer, faDatabase, faDiagramProject, 
  faCode, faBolt, faShield, faPhone, faLocationDot, faPaperPlane, faCreditCard, 
  faBars, faXmark as faTimes, faExclamationCircle, faCircleNotch, faCheckCircle, 
  faInfoCircle, faGlobe, faArrowLeft, faRocket, faSearch, faCalendar, faChevronDown, 
  faTachometerAlt, faNewspaper, faPlusCircle, faFolder, faPlus, faEdit, faTrashAlt, 
  faEye, faMagic, faSave, faLock, faSignOutAlt, faFolderPlus, faRss, faDonate, faCog,
  faChartLine, faUsers, faDownload, faGraduationCap, faList, faBan, faCheck,
  faUserGraduate, faClock, faMoneyBillWave, faUndo, faTrash, faArrowRight,
  faCommentSlash, faRedo, faUser, faComments, faReply,
  // Block editor icons
  faBold, faItalic, faUnderline, faHeading, faParagraph, faListUl, faListOl, 
  faAlignLeft, faAlignCenter, faAlignRight, faAlignJustify, faQuoteRight, faMinus, 
  faImage, faLink, faUnlink,
  // Block menu icons
  faVideo, faMusic, faTable,
  // File uploader icon
  faCloudUploadAlt, faUpload
} from '@fortawesome/free-solid-svg-icons'

import { faGithub, faYoutube, faLinkedin, faTwitter, faTelegram } from '@fortawesome/free-brands-svg-icons'

/* Add icons to the library */
library.add(
  faEnvelope, faHome, faHeart, faBook, faServer, faDatabase, faDiagramProject, 
  faCode, faBolt, faShield, faGithub, faYoutube, faLinkedin, faPhone, 
  faLocationDot, faPaperPlane, faCreditCard, faBars, faTimes, faExclamationCircle, 
  faCircleNotch, faCheckCircle, faInfoCircle, faGlobe, faArrowLeft, faRocket, faSearch, 
  faCalendar, faTwitter, faTelegram, faChevronDown,
  faChartLine, faEye, faUsers, faNewspaper,
  // Admin icons
  faTachometerAlt, faNewspaper, faPlusCircle, faFolder, faPlus, faEdit, 
  faTrashAlt, faEye, faMagic, faSave, faLock, faSignOutAlt, faFolderPlus, faRss,
  // Block editor icons
  faBold, faItalic, faUnderline, faHeading, faParagraph, faListUl, faListOl, 
  faAlignLeft, faAlignCenter, faAlignRight, faAlignJustify, faQuoteRight, faMinus, 
  faImage, faLink, faUnlink, faUndo, faRedo,
  // Block menu icons
  faVideo, faMusic, faTable,
  // File uploader icon
  faCloudUploadAlt, faUpload,
  // Comment icons
  faUser, faComments, faCommentSlash, faReply, faClock,
  // New icons
  faDonate, faCog,
  // Additional missing icons
  faDownload, faGraduationCap, faList, faBan, faCheck, faUserGraduate, 
  faMoneyBillWave, faUndo, faTrash, faArrowRight, faRedo
)

const app = createApp(App)

/* Add FontAwesome component */
app.component('font-awesome-icon', FontAwesomeIcon)

app.use(router)
app.use(Particles)

app.mount('#app')
