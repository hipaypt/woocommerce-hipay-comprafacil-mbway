if (typeof window.wc !== 'undefined' && window.wc.wcBlocksRegistry && typeof wp.i18n !== 'undefined' && typeof React !== 'undefined') {

    const { registerPaymentMethod } = window.wc.wcBlocksRegistry;

    const HipayMbway = {
        name: 'hipaymbway', 
        label: wp.i18n.__('MB WAY', 'hipaymbway'), 
        content: React.createElement('div', null, wp.i18n.__('Pay with MB WAY.', 'hipaymbway'), hipayMbwayData.image && React.createElement('img', { src: hipayMbwayData.image, alt: '', style: { marginLeft: '10px' } })), 
        edit: React.createElement('div', null, wp.i18n.__('Pay with MB WAY.', 'hipaymbway'), hipayMbwayData.image && React.createElement('img', { src: hipayMbwayData.image, alt: '', style: { marginLeft: '10px' } })), 
        canMakePayment: () => true, 
        ariaLabel: wp.i18n.__('MB WAY', 'hipaymbway'), 
        supports: {
            features: ['products'], 
        },
    };

    registerPaymentMethod( HipayMbway );
}