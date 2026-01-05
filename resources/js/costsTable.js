export default function costsTable() {
    return {
        // filtros
        search: '',
        filterEmpresa: '',

        // modal
        showModal: false,
        costId: null,
        categoria: '',
        month: '',
        valor: 0,
        valor_atual: 0,
        file: null,
        file_url: null,
        status: 1,
        rowVisible(categoria, empresa) {
    const search = this.search.toLowerCase().trim();
    const filtroEmpresa = this.filterEmpresa.toLowerCase().trim();

    const matchSearch =
        !search || categoria.includes(search);

    const matchEmpresa =
        !filtroEmpresa || empresa === filtroEmpresa;

    return matchSearch && matchEmpresa;
},


        // Abre o modal a partir de data-* do HTML
        openNotaModalFromElement(el) {
            const target = el.currentTarget;
            const id = target.dataset.id;
            const categoria = target.dataset.categoria;
            const valor = parseFloat(target.dataset.valor);
            const month = target.dataset.month;

            this.openNotaModal(id, categoria, valor, month);
        },

        // Função original que já existia
        openNotaModal(id, categoria, valor, month) {
            console.log('MODAL', id, categoria, valor, month);

            this.costId = id;
            this.categoria = categoria;
            this.month = month;
            this.valor_atual = Number(valor) || 0;
            this.valor = this.valor_atual;
            this.showModal = true;

            this.loadNota();
        },

        closeModal() {
            this.showModal = false;
        },

        async loadNota() {
            try {
                const csrf = document
                    .querySelector('meta[name=csrf-token]')
                    ?.content;

                const r = await fetch(
                    `/financeiro/notas/${this.costId}/${this.month}`,
                    { headers: { 'X-CSRF-TOKEN': csrf } }
                );

                if (!r.ok) return;

                const data = await r.json();
                this.valor = Number(data.valor ?? this.valor_atual);
                this.file_url = data.file_url ?? null;
                this.status = Number(data.status ?? 1);
            } catch (e) {
                console.error(e);
            }
        },

        async saveNota() {
            const csrf = document
                .querySelector('meta[name=csrf-token]')
                ?.content;

            const form = new FormData();
            form.append('valor', this.valor);
            form.append('status', this.status);

            if (this.file) {
                form.append('file', this.file);
            }

            const r = await fetch(
                `/financeiro/notas/${this.costId}/${this.month}`,
                {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf },
                    body: form,
                }
            );

            if (!r.ok) {
                alert('Erro ao salvar');
                return;
                
            }

            this.closeModal();
            location.reload();
        }
        
    };
}
