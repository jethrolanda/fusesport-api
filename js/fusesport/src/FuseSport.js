import { useState, useEffect } from "react";
import { Button } from "antd";

const App = () => {
  const [loading, setLoading] = useState(false);
  const test = () => {
    async function testRequest() {
      try {
        setLoading(true);
        const response = await fetch(fusesport_params.ajax_url, {
          method: "POST",
          headers: {
            "X-WP-Nonce": fusesport_params.nonce,
            "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"
          },
          body: new URLSearchParams({
            action: "fusesport_api"
          })
        });

        if (!response.ok) throw new Error("API request failed");
        const { status, data } = await response.json();

        console.log(data);
        if (status === "success") {
        }
      } catch (error) {
        console.error("Error fetching orders:", error);
      } finally {
        setLoading(false);
      }
    }

    testRequest();
  };

  return (
    <>
      <Button onClick={test} loading={loading}>
        Test Call
      </Button>
    </>
  );
};
export default App;
