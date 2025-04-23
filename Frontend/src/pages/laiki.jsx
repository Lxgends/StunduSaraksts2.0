import "../css/laiki.css";
import axios from 'axios';
import { useLocation } from 'preact-iso';
import { useEffect, useState } from 'preact/hooks';
import moment from 'moment';

function Laiki() {
    const [laiksData, setLaiksData] = useState([]);
    const [error, setError] = useState(null);
    const [loading, setLoading] = useState(true);

    const { url } = useLocation();

    useEffect(() => {
        const fetchData = async () => {
            const token = localStorage.getItem('token');
            const config = {
                headers: {
                    'Authorization': token ? `Bearer ${token}` : '',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            };

            setLoading(true);
            try {
                const laiksResponse = await axios.get('http://localhost:8000/api/laiks', config);
                setLaiksData(laiksResponse.data || []);
                setError(null);
            } catch (err) {
                console.error('API Error:', err.response?.data || err.message);
                setError(err.message || 'Failed to fetch data');
            } finally {
                setLoading(false);
            }
        };

        fetchData();
    }, []);

    const renderClassTimes = (startId, endId, startingNumber = 1) => {
        let classNumber = startingNumber;
        return laiksData
            .filter(item => item.id >= startId && item.id <= endId)
            .map((item) => (
                <div className="classTime" key={item.id}>
                    <strong className="classTitle"><p>{classNumber++}. Pārstunda</p></strong>
                    <strong><p>{moment(item.sakumalaiks, 'HH:mm:ss').format('HH:mm')} - {moment(item.beigulaiks, 'HH:mm:ss').format('HH:mm')}</p></strong>
                </div>
            ));
    };

    return (
        <div className="laikimain">
            {error && <div className="error-message">Error: {error}</div>}
            {loading ? (
                <p>Ielādē...</p>
            ) : (
                <>
                    <div className="pirmdiena">
                        <strong className="titleb">Pirmdiena - Ceturtdiena</strong>
                        {renderClassTimes(1, 2)}
                        {laiksData.find(item => item.id === 11) && (
                            <div className="pusdTime">
                                <strong className="breakTitle">Pusdienu pārtraukums</strong>
                                <strong>
                                    <p>
                                        {moment(laiksData.find(item => item.id === 11).sakumalaiks, 'HH:mm:ss').format('HH:mm')} -{' '}
                                        {moment(laiksData.find(item => item.id === 11).beigulaiks, 'HH:mm:ss').format('HH:mm')}
                                    </p>
                                </strong>
                            </div>
                        )}
                        {renderClassTimes(3, 5, 3)}
                    </div>

                    <div className="piektdiena">
                        <strong className="titleb">Piektdiena</strong>
                        {renderClassTimes(6, 8)}
                        {laiksData.find(item => item.id === 12) && (
                            <div className="pusdTime">
                                <strong className="breakTitle">Pusdienu pārtraukums</strong>
                                <strong>
                                    <p>
                                        {moment(laiksData.find(item => item.id === 12).sakumalaiks, 'HH:mm:ss').format('HH:mm')} -{' '}
                                        {moment(laiksData.find(item => item.id === 12).beigulaiks, 'HH:mm:ss').format('HH:mm')}
                                    </p>
                                </strong>
                            </div>
                        )}
                        {renderClassTimes(9, 10, 4)}
                    </div>
                </>
            )}
        </div>
    );
}

export default Laiki;