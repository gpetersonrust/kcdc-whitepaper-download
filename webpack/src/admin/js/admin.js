import '../scss/admin.scss';

class KCDCWhitepaperExporter {
    constructor(formId, selectId, nonceFieldId) {
        this.form = document.getElementById(formId);
        this.select = document.getElementById(selectId);
        this.nonceField = document.getElementById(nonceFieldId);

        if (!this.form || !this.select || !this.nonceField) {
            console.error('KCDCWhitepaperExporter: Required elements not found.');
            return;
        }

        this.registerEvents();
    }

    registerEvents() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    async handleSubmit(event) {
        event.preventDefault();

        const postId = this.select.value;
        const nonce = this.nonceField.value;

        if (!postId) {
            alert('Please select a whitepaper.');
            return;
        }

        try {
            const response = await fetch(ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    action: 'kcdc_export_whitepaper_csv',
                    post_id: postId,
                    security: nonce
                })
            });

            if (!response.ok) {
                throw new Error(`Server responded with status ${response.status}`);
            }

            const blob = await response.blob();

            if (blob.type !== "text/csv") {
                throw new Error("Unexpected file format received.");
            }

            this.downloadCSV(blob, postId);
        } catch (error) {
            console.error('Export failed:', error);
            alert('An error occurred while exporting. Please try again.');
        }
    }

    downloadCSV(blob, postId) {
        const url = window.URL.createObjectURL(blob);
        const anchor = document.createElement('a');
        anchor.href = url;
        anchor.download = `whitepaper_requests_post_${postId}.csv`;
        document.body.appendChild(anchor);
        anchor.click();
        anchor.remove();
        window.URL.revokeObjectURL(url);
    }
}


document.addEventListener('DOMContentLoaded', () => {
    const exporter = new KCDCWhitepaperExporter('kcdc-whitepaper-exporter-form', 'kcdc-whitepaper-select', 'kcdc-whitepaper-exporter-nonce');
});