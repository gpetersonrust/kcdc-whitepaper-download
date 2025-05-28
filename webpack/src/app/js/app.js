import '../scss/app.scss'; // Import SCSS
 

// class WhitepaperDownloader {
//   constructor(action) {
//     this.token = new URLSearchParams(window.location.search).get('token');
//     this.postId = new URLSearchParams(window.location.search).get('post_id');
//     this.container = document.querySelector('.kcdc-download-wrapper');
//     this.action = action;
//   }

//   async fetchDownload() {
//     try {



//       const response = await fetch(`/wp-json/kcdc-whitepaper/v1/download?token=${this.token}&post_id=${this.postId}&action=${this.action}`);
//       const data = await response.json();

//       if (!response.ok) {
//         throw new Error(data.message || 'Download failed');
//       }

//       if (data.success) {
//         this.renderDocuments(data.documents, data.messages);
//       }
//     } catch (error) {
//       this.renderError(error.message);
//     }
//   }

//   renderDocuments(documents, messages) {
//     if (!documents || documents.length === 0) {
//       this.container.innerHTML = `<p>${messages.no_documents}</p>`;
//       return;
//     }

//     const html = `
//       <h2 class="kcdc-download-heading">${messages.success}</h2>
//       <div class="kcdc-download-documents">
//         ${documents.map(doc => `
//           <a href="${doc.link}" class="kcdc-document-link" download>
//             ${doc.name}
//           </a>
//         `).join('')}
//       </div>
//     `;
//     this.container.innerHTML = html;
//   }

//   renderError(message) {
//     this.container.innerHTML = `
//       <div class="kcdc-download-error">
//         <p>${message}</p>
//       </div>
//     `;
//   }

//   init() {
//     if (this.token && this.postId && this.action === 'kcdc_download_whitepaper') {
//       this.fetchDownload();
//     }
//   }
// }

// // Initialize when DOM is ready
// document.addEventListener('DOMContentLoaded', () => {

//   const action = new URLSearchParams(window.location.search).get('action');
//   if (action === 'kcdc_download_whitepaper') {
//     const downloader = new WhitepaperDownloader(action);
//     downloader.init();
//   }
// });
 
