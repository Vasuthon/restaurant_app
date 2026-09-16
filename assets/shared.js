// เมธอดที่ใช้ร่วมกันระหว่างหน้าบ้าน (index.php) และหลังบ้าน (staff.php)
const SharedAppMethods = {
    formatNumber(num) {
        return (num || 0).toLocaleString()
    },
    formatTimeLeft(seconds) {
        if (!seconds || seconds <= 0) return '00:00'
        const hrs = Math.floor(seconds / 3600)
        const mins = Math.floor((seconds % 3600) / 60)
        const secs = seconds % 60
        return `${hrs > 0 ? hrs + ':' : ''}${mins < 10 ? '0' : ''}${mins}:${secs < 10 ? '0' : ''}${secs}`
    },
    getStatusText(status) {
        const map = { pending: 'รอดำเนินการ', cooking: 'กำลังปรุง', served: 'เสิร์ฟแล้ว' }
        return map[status] || status
    },
    getStatusBadgeClass(status) {
        const map = {
            pending: 'bg-amber-900/60 text-amber-300 border border-amber-700',
            cooking: 'bg-blue-900/60 text-blue-300 border border-blue-700',
            served: 'bg-emerald-900/60 text-emerald-300 border border-emerald-700'
        }
        return map[status] || ''
    },
    async apiGet(url) {
        const res = await fetch(url)
        if (res.status === 401 && this.handleAuthExpired) this.handleAuthExpired()
        return res.json()
    },
    async apiPost(url, body) {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        })
        if (res.status === 401 && this.handleAuthExpired) this.handleAuthExpired()
        return res.json()
    }
}
