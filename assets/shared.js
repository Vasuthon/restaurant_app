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
            pending: 'bg-amber-50 text-amber-700 border border-amber-300',
            cooking: 'bg-blue-50 text-blue-700 border border-blue-300',
            served: 'bg-emerald-50 text-emerald-700 border border-emerald-300'
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
