import "../css/laiki.css"

function Laiki() {
    return (
        <div className="laikimain">
            <div className="pirmdiena">
                <b className="titleb">Pirmdiena - Ceturtdiena</b>
                <b className="mainb"><p className="mainp">1. Pārstunda </p> 8:30 - 9:50</b>
                <b className="mainb"><p className="mainp">2. Pārstunda </p> 10:10 - 11:30</b>
                <b><p>Pusdienu pārtraukums</p> 11:30 - 12:30</b>
                <b className="mainb"><p className="mainp">3. Pārstunda </p> 12:30 - 13:50</b>
                <b className="mainb"><p className="mainp">4. Pārstunda </p> 14:00 - 15:20</b>
                <b className="mainb"><p className="mainp">5. Pārstunda </p> 15:30 - 16:50</b>
            </div>
            <div className="piektdiena">
                <b className="titleb">Piektdiena</b>
                <b className="mainb"><p className="mainp">1. Pārstunda </p> 8:10 - 9:30</b>
                <b className="mainb"><p className="mainp">2. Pārstunda </p> 9:40 - 11:00</b>
                <b className="mainb"><p className="mainp">3. Pārstunda </p> 11:10 - 12:30</b>
                <b><p>Pusdienu pārtraukums</p> 12:30 - 13:00</b>
                <b className="mainb"><p className="mainp">4. Pārstunda </p> 13:00 - 14:20</b>
                <b className="mainb"><p className="mainp">5. Pārstunda </p> 14:30 - 15:50</b>
            </div>
        </div>
    );
}

export default Laiki;