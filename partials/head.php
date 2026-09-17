<?php $pageTitle = $pageTitle ?? 'HOTPOT MAN - Buffet POS System'; ?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?></title>
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- Vue 3 -->
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<!-- FontAwesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    dark: {
                        base: '#F7F2EA',
                        card: '#FFFFFF',
                        border: '#E8DFCF',
                        hover: '#EEE6D8'
                    },
                    accent: {
                        crimson: '#A31E22',
                        gold: '#A67C00',
                        emerald: '#2E7D32'
                    }
                }
            }
        }
    }
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;800;900&display=swap');
    [v-cloak] { display: none !important; }
    body { font-family: 'Prompt', sans-serif; background: #F7F2EA !important; color: #1C1815 !important; }

    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #EEE6D8; }
    ::-webkit-scrollbar-thumb { background: #A31E22; border-radius: 8px; }

    #receiptPrintArea { display: none; }
    @media print {
        body * { visibility: hidden; }
        #receiptPrintArea { display: block !important; visibility: visible; position: absolute; left: 0; top: 0; width: 100%; }
        #receiptPrintArea * { visibility: visible; }
    }
</style>
