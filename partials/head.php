<?php $pageTitle = $pageTitle ?? 'ชาบูหมาล่าบุฟเฟต์ - Buffet POS System'; ?>
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
                        base: '#0D0D0D',
                        card: '#1B1B1B',
                        border: '#353535',
                        hover: '#2A2A2A'
                    },
                    accent: {
                        crimson: '#D32F2F',
                        gold: '#D4AF37',
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
    body { font-family: 'Prompt', sans-serif; background: #151312 !important; color: #F7F1E8 !important; }

    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #111; }
    ::-webkit-scrollbar-thumb { background: #4A2A26; border-radius: 8px; }
</style>
